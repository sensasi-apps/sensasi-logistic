<?php

namespace App\Repositories;

use App\Models\ProductIn;
use App\Models\ProductInDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProductInRepository extends BaseRepository
{
	protected string $urlParamName = 'product_in';
	protected string $modelClass = ProductIn::class;

	protected array $withs = [
		'details.product',
		'details.outDetails',
		'details.stock'
	];

	public function store(array $productIn): ProductIn
	{
		$validatedData = $this->validateData($productIn);
		$validatedDetails = $this->validateDetailsData($productIn['details']);

		try {
			DB::beginTransaction();

			$this->workingInstance = $this->workingInstance::create($validatedData);
			$this->addDataIdToArray($validatedDetails);

			$this->workingInstance->details()->insert($validatedDetails);
		} catch (\Throwable $th) {
			DB::rollBack();
			throw $th;
		}

		DB::commit();

		return $this->workingInstance;
	}

	public function update(array $productIn): ProductIn
	{
		$validatedData = $this->validateData($productIn);
		$validatedDetails = $this->validateDetailsData($productIn['details']);

		[
			'forInsert' => $forInsert,
			'forUpdate' => $forUpdate,
			'forDelete' => $forDelete
		] = $this->separateDetailsData($validatedDetails);

		$this->validateDeleteDetails($forDelete->toArray());

		try {
			DB::beginTransaction();

			// update product_in
			$this->workingInstance->update($validatedData);

			$forUpsert = $forInsert->merge($forUpdate)->toArray();

			$this->addDataIdToArray($forUpsert);

			// update/insert data from user input
			ProductInDetail::upsert(
				$forUpsert,
				['product_in_id', 'product_id'],
				['qty', 'price', 'expired_at', 'manufactured_at']
			);

			// delete record that not exists in $detailsData
			$this->workingInstance->details()->whereIn('id', $forDelete->pluck('id')->toArray())->delete();

			DB::commit();
		} catch (\Throwable $th) {
			DB::rollBack();
			throw $th;
		}

		return $this->workingInstance;
	}

	public function destroy(): ProductIn
	{
		$this->validateDeleteData();
		$this->validateDeleteDetails();

		try {
			DB::beginTransaction();

			$this->workingInstance->details()->delete();
			$this->workingInstance->delete();

			DB::commit();
		} catch (\Throwable $th) {
			DB::rollBack();
			throw $th;
		}

		return $this->workingInstance;
	}

	// VALIDATION
	private function validateData(array $productIn): array
	{
		return Validator::make($productIn, [
			'code' => "nullable|string|unique:product_ins,code,{$this->workingInstance->id}",
			'type' => 'required|string',
			'note' => 'nullable|string',
			'at' => 'required|date'
		])->validate();
	}

	private function validateDetailsData(array $productInDetails): array
	{
		$existsDetails = $this->workingInstance->details->keyBy('product_id');

		return Validator::make($productInDetails, [
			'*.product_id' => Rule::forEach(function ($value) use ($existsDetails) {

				$rule = Rule::unique('product_in_details')->where(function ($query) use ($value) {
					return $query->where([
						'product_in_id' => $this->workingInstance->id,
						'product_id' => $value
					]);
				});

				if ($existsDetails->get($value)) {
					$rule->ignore($existsDetails->get($value)->id);
				}

				return [
					'required',
					'distinct',
					'exists:products,id',
					$rule
				];
			}),

			'*.qty' => Rule::forEach(function ($value, $attr, $item) use ($existsDetails) {

				$index = explode('.', $attr)[0];
				$productId = $item["{$index}.product_id"];
				$productInDetail = $existsDetails->get($productId);

				$min = $productInDetail ? $productInDetail->outDetails->sum('qty') : 0;

				return [
					'required',
					'numeric',
					"min:{$min}"
				];
			}),

			'*.price' => 'required|numeric|min:0',
			// TODO: implement check if expired_at is after at main: after_or_equal:{$productIn['at']}
			'*.expired_at' => "nullable|date",
			// TODO: implement check if manufactured_at is before at main: before_or_equal:{$productIn['at']}
			'*.manufactured_at' => "nullable|date"
		])->validate();
	}

	private function validateDeleteData(): array
	{
		return Validator::make($this->workingInstance->toArray(), [
			'id' => "unique:product_manufactures,product_in_id,{$this->workingInstance->id},product_in_id",
		])->validate();
	}

	private function validateDeleteDetails(array $productInDetails = null): array
	{
		return Validator::make(($productInDetails ?? $this->workingInstance->details->toArray()), [
			'*.id' => 'unique:product_out_details,product_in_detail_id',
		])->validate();
	}

	private function separateDetailsData(array $detailsData): array
	{
		$detailsData = collect($detailsData);
		$existsProductInDetails = $this->workingInstance->details->keyBy('product_id');
		$detailsDataCollection = $detailsData->keyBy('product_id');
		$existsProductIds = $existsProductInDetails->pluck('product_id');
		$detailsDataProductIds = $detailsDataCollection->pluck('product_id');

		$t1 = $detailsDataProductIds->diff($existsProductIds);
		$t2 = $detailsDataProductIds->intersect($existsProductIds);
		$t3 = $existsProductIds->diff($detailsDataProductIds);

		return [
			'forInsert' => $detailsDataCollection->whereIn('product_id', $t1->toArray()),
			'forUpdate' => $detailsDataCollection->whereIn('product_id', $t2->toArray()),
			'forDelete' => $existsProductInDetails->whereIn('product_id', $t3->toArray())
		];
	}
}
