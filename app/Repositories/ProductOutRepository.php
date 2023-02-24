<?php

namespace App\Repositories;

use App\Models\ProductOut;
use App\Models\ProductOutDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Repositories\Traits\ProductOutTrait;

class ProductOutRepository
{
	use ProductOutTrait;

	public function __construct(
		private ProductOut $model
	) {
	}

	private function setWorkingInstance(): void
	{
		$this->workingInstance = $this->retrieveWorkingInstance();
	}

	private function retrieveWorkingInstance(): ProductOut
	{
		if (Route::current() == null) {
			return $this->model;
		}

		$productOutId = Route::current()->parameter('product_out');

		if ($productOutId == null) {
			return $this->model;
		}

		$withs = [
			'details.productInDetail' => [
				'stock',
				'product',
				'productIn'
			],
		];

		return $this->model::with($withs)->findOrFail($productOutId);
	}

	public function store(array $data, array $detailsData): ProductOut
	{
		$this->setWorkingInstance();

		$validatedData = $this->validateData($data);
		$validatedDetailsData = $this->validateDetailsData($detailsData);

		try {
			DB::beginTransaction();

			$this->workingInstance = $this->model::create($validatedData);
			$this->addDataIdToDetails($validatedDetailsData);

			$this->workingInstance->details()->insert($validatedDetailsData);
			DB::commit();
		} catch (\Throwable $th) {
			DB::rollBack();
			throw $th;
		}

		return $this->workingInstance->fresh();
	}

	/**
	 * Update exists ProductOut and ProductOutDetails in database
	 *
	 * @param array $data input from request
	 * @param array $detailsData input from request
	 * @return ProductOut
	 **/
	public function update(array $data, array $detailsData): ProductOut
	{
		$this->setWorkingInstance();

		$validatedData = $this->validateData($data);
		$validatedDetails = $this->validateDetailsData($detailsData);

		[
			'forInsert' => $forInsert,
			'forUpdate' => $forUpdate,
			'forDelete' => $forDelete
		] = $this->separateDetailsData(collect($validatedDetails));

		try {
			DB::beginTransaction();

			$this->workingInstance->update($validatedData);

			$forUpsert = $forInsert->merge($forUpdate)->toArray();
			$this->addDataIdToDetails($forUpsert);

			ProductOutDetail::upsert(
				$forUpsert,
				['product_out_id', 'product_in_id'],
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
		$this->setWorkingInstance();

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
}
