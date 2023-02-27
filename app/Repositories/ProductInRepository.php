<?php

namespace App\Repositories;

use App\Models\ProductIn;
use App\Models\ProductInDetail;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProductInRepository extends BaseRepository
{
	protected string $urlParamName = 'material_out';
	protected string $modelClass = MaterialOut::class;

	protected array $withs = [
		'details.materialInDetail' => [
			'stock',
			'material',
			'materialIn'
		],
	];

	public function __construct(
		private ProductIn $model
	) {
		$this->setWorkingInstance();
	}

	private function setWorkingInstance(ProductIn $productIn = null): void
	{
		$this->workingInstance = $this->retrieveWorkingInstance();
	}

	private function retrieveWorkingInstance(): ProductIn
	{
		if (Route::current() == null) {
			return $this->model;
		}

		$productInId = Route::current()->parameter('product_in');

		if ($productInId == null) {
			return $this->model;
		}

		$with = [
			'details.product',
			'details.outDetails',
			'details.stock'
		];

		return $this->model::with($with)->findOrFail($productInId);
	}

	public function store(array $data, array $detailsData): ProductIn
	{
		$validatedData = $this->validateData($data);
		$validatedDetailsData = $this->validateDetailsData($detailsData);

		try {
			DB::beginTransaction();

			$this->workingInstance = $this->model::create($validatedData);
			$this->addDataIdToDetails($validatedDetailsData);

			$this->workingInstance->details()->insert($validatedDetailsData);
		} catch (\Throwable $th) {
			DB::rollBack();
			throw $th;
		}

		DB::commit();

		return $this->workingInstance->fresh();
	}

	public function update(array $data, array $detailsData): ProductIn
	{
		if (!$this->workingInstance->id && $data['id']) {
			$this->setWorkingInstance(ProductIn::findOrFail($data['id']));
		}

		$validatedData = $this->validateData($data);
		$validatedDetails = $this->validateDetailsData($detailsData);

		[
			'forInsert' => $forInsert,
			'forUpdate' => $forUpdate,
			'forDelete' => $forDelete
		] = $this->separateDetailsData(collect($validatedDetails));

		$this->validateForDelete($data, $forDelete->toArray());

		try {
			DB::beginTransaction();

			// update product_in
			$this->workingInstance->update($validatedData);

			$forUpsert = $forInsert->merge($forUpdate)->toArray();

			$this->addDataIdToDetails($forUpsert);

			// update/insert data from user input
			ProductInDetail::upsert(
				$forUpsert,
				['product_in_id', 'product_id'],
				['qty', 'price']
			);

			// delete record that not exists in $detailsData
			$this->workingInstance->details()->whereIn('id', $forDelete->pluck('id')->toArray())->delete();

			DB::commit();
		} catch (\Throwable $th) {
			DB::rollBack();
			throw $th;
		}

		return $this->workingInstance->fresh();
	}

	public function destroy(): ProductIn
	{
		$this->setWorkingInstance();

		$details = $this->workingInstance->details;
		$this->validateForDelete($this->workingInstance->toArray(), $details->toArray());

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

	private function validateData(array $data): array
	{
		return Validator::make($data, [
			'code' => "nullable|string|unique:product_ins,code,{$this->workingInstance->id}",
			'type' => 'required|string',
			'note' => 'nullable|string',
			'at' => 'required|date'
		])->validate();
	}

	private function validateDetailsData(array $detailsData): array
	{
		$existsDetails = $this->workingInstance->details->keyBy('product_id');

		return Validator::make($detailsData, [
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

			'*.price' => 'required|numeric|min:0'
		])->validate();
	}


	private function validateForDelete(array $data, array $detailsData): array
	{
		return [
			Validator::make($data, [
				'id' => "unique:manufactures,product_in_id,{$this->workingInstance->id},product_in_id",
			])->validate(),
			Validator::make($detailsData, [
				'*.id' => 'unique:product_out_details,product_in_detail_id',
			])->validate()
		];
	}

	private function separateDetailsData(Collection $detailsData): array
	{
		$existsProductInDetails = $this->workingInstance->details->keyBy('product_id');
		$detailsDataCollection = $detailsData->keyBy('product_id');
		$existsProductIds = $existsProductInDetails->pluck('product_id');
		$detailsDataProductIds = $detailsDataCollection->pluck('product_id');

		// get product ids that not exists in old products
		$t1 = $detailsDataProductIds->diff($existsProductIds);
		// get product ids that exists in old products
		$t2 = $detailsDataProductIds->intersect($existsProductIds);
		// get product ids that not exists in new products
		$t3 = $existsProductIds->diff($detailsDataProductIds);

		return [
			'forInsert' => $detailsDataCollection->whereIn('product_id', $t1->toArray()),
			'forUpdate' => $detailsDataCollection->whereIn('product_id', $t2->toArray()),
			'forDelete' => $existsProductInDetails->whereIn('product_id', $t3->toArray())
		];
	}

	private function addDataIdToDetails(array &$detailsData): void
	{
		foreach ($detailsData as &$detailData) {
			if (!$this->workingInstance->id) {
				$this->workingInstance->refresh();
			}

			$detailData['product_in_id'] = $this->workingInstance->id;
		}
	}
}
