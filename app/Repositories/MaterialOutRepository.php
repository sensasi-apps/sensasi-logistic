<?php

namespace App\Repositories;

use App\Models\MaterialOut;
use App\Models\MaterialOutDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Repositories\Traits\MaterialOutTrait;

class MaterialOutRepository
{
	use MaterialOutTrait;

	public function __construct(
		private MaterialOut $model
	) {
	}

	private function setWorkingInstance(): void
	{
		$this->workingInstance = $this->retrieveWorkingInstance();
	}

	private function retrieveWorkingInstance(): MaterialOut
	{
		if (Route::current() == null) {
			return $this->model;
		}

		$materialOutId = Route::current()->parameter('material_out');

		if ($materialOutId == null) {
			return $this->model;
		}

		$withs = [
			'details.materialInDetail' => [
				'stock',
				'material',
				'materialIn'
			],
		];

		return $this->model::with($withs)->findOrFail($materialOutId);
	}

	public function store(array $data, array $detailsData): MaterialOut
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
	 * Update exists MaterialOut and MaterialOutDetails in database
	 *
	 * @param array $data input from request
	 * @param array $detailsData input from request
	 * @return MaterialOut
	 **/
	public function update(array $data, array $detailsData): MaterialOut
	{
		$this->setWorkingInstance();

		$validatedData = $this->validateData($data);
		$validatedDetails = $this->validateDetailsData($detailsData);

		[
			'forInsert' => $forInsert,
			'forUpdate' => $forUpdate,
			'forDelete' => $forDelete
		] = $this->separateDetailsData(collect($validatedDetails));

		$this->validateForDelete($forDelete->toArray());

		try {
			DB::beginTransaction();

			$this->workingInstance->update($validatedData);

			$forUpsert = $forInsert->merge($forUpdate)->toArray();
			$this->addDataIdToDetails($forUpsert);

			MaterialOutDetail::upsert(
				$forUpsert,
				['material_out_id', 'material_in_id'],
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

	public function destroy(): MaterialOut
	{
		$this->setWorkingInstance();

		$this->validateForDelete($this->workingInstance->toArray());

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
