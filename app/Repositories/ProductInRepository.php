<?php

namespace App\Repositories;

use App\Models\ProductIn;
use App\Models\ProductInDetail;
use App\Repositories\Traits\ProductInTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class ProductInRepository
{
	use ProductInTrait;

	public function __construct(
		private ProductIn $model
	) {
	}

	private function setWorkingInstance(): void
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
		$this->setWorkingInstance();

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
		$this->setWorkingInstance();

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
}
