<?php

namespace App\Repositories;

use App\Models\ProductOut;
use App\Models\ProductOutDetail;
use App\Models\Views\ProductInDetailsStockView;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProductOutRepository extends BaseRepository
{
	protected string $urlParamName = 'product_out';
	protected string $modelClass = ProductOut::class;

	protected array $withs = [
		'details.productInDetail' => [
			'stock',
			'product',
			'productIn'
		],
	];


	public function store(array $productOut): ProductOut
	{
		$validatedData = $this->validateData($productOut);
		$validatedDetailsData = $this->validateDetailsData($productOut['details']);

		try {
			DB::beginTransaction();

			$this->workingInstance = $this->workingInstance::create($validatedData);
			$this->addDataIdToArray($validatedDetailsData);

			$this->workingInstance->details()->insert($validatedDetailsData);
			DB::commit();
		} catch (\Throwable $th) {
			DB::rollBack();
			throw $th;
		}

		return $this->workingInstance;
	}

	public function update(array $productOut): ProductOut
	{
		$validatedData = $this->validateData($productOut);
		$validatedDetailsData = $this->validateDetailsData($productOut['details']);

		[
			'forInsert' => $forInsert,
			'forUpdate' => $forUpdate,
			'forDelete' => $forDelete
		] = $this->separateDetailsData($validatedDetailsData);

		try {
			DB::beginTransaction();

			$this->workingInstance->update($validatedData);

			$forUpsert = $forInsert->merge($forUpdate)->toArray();
			$this->addDataIdToArray($forUpsert);

			ProductOutDetail::upsert(
				$forUpsert,
				["{$this->urlParamName}_id", 'product_in_id'],
				['qty']
			);

			$this->workingInstance->details()->whereIn('id', $forDelete->pluck('id')->toArray())->delete();

			DB::commit();
		} catch (\Throwable $th) {
			DB::rollBack();
			throw $th;
		}

		return $this->workingInstance->fresh();
	}

	public function destroy(): ProductOut
	{
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

	private function validateData(array $productOut): array
	{
		return Validator::make($productOut, [
			'code' => "nullable|string|unique:product_outs,code,{$this->workingInstance->id}",
			'type' => 'required|string',
			'note' => 'nullable|string',
			'at' => 'required|date'
		])->validate();
	}

	private function validateDetailsData(array $detailsData): array
	{
		$existsDetails = $this->workingInstance->details->keyBy('product_in_detail_id');
		$productInDetailsStock = ProductInDetailsStockView::whereIn('product_in_detail_id', array_column($detailsData, 'product_in_detail_id'))->get()->keyBy('product_in_detail_id');

		return Validator::make($detailsData, [
			'*.product_in_detail_id' => Rule::forEach(function ($value) use ($existsDetails) {

				$rule = Rule::unique('product_out_details')->where(function ($query) use ($value) {
					return $query->where([
						"{$this->urlParamName}_id" => $this->workingInstance->id,
						'product_in_detail_id' => $value
					]);
				});

				if ($existsDetails->get($value)) {
					$rule->ignore($existsDetails->get($value)->id);
				}

				return [
					'required',
					'distinct',
					'exists:product_in_details,id',
					$rule
				];
			}),

			'*.price' => [
				'required',
				'numeric',
				'min:0'
			],

			'*.qty' => Rule::forEach(function ($value, $attr, $item) use ($productInDetailsStock, $existsDetails) {

				$index = explode('.', $attr)[0];
				$productInDetailId = $item["{$index}.product_in_detail_id"];

				$max = $productInDetailsStock->get($productInDetailId)->qty;

				if ($existsDetails->get($productInDetailId)) {
					$max += $existsDetails->get($productInDetailId)->qty;
				}

				return [
					'required',
					'numeric',
					"max:{$max}",
				];
			}),
		])->validate();
	}

	private function separateDetailsData(array $detailsData): array
	{
		$detailsDataCollection = collect($detailsData);
		$detailsDataProductInIds = $detailsDataCollection->pluck('product_in_detail_id');

		$existsProductOutDetails = $this->workingInstance->details;
		$existsProductInIds = $existsProductOutDetails->pluck('product_in_detail_id');

		// get product ids that not exists in old products
		$t1 = $detailsDataProductInIds->diff($existsProductInIds);
		// get product ids that exists in old products
		$t2 = $detailsDataProductInIds->intersect($existsProductInIds);
		// get product ids that not exists in new products
		$t3 = $existsProductInIds->diff($detailsDataProductInIds);

		return [
			'forInsert' => $detailsDataCollection->whereIn('product_in_detail_id', $t1->toArray()),
			'forUpdate' => $detailsDataCollection->whereIn('product_in_detail_id', $t2->toArray()),
			'forDelete' => $existsProductOutDetails->whereIn('product_in_detail_id', $t3->toArray())
		];
	}
}
