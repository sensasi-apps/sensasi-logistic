<?php

namespace App\Repositories;

use App\Models\MaterialIn;
use App\Models\MaterialInDetail;
use App\Repositories\Traits\MaterialInTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;


class MaterialInRepository extends BaseRepository
{
	use MaterialInTrait;

	/**
	 * Create a new repository instance.
	 *
	 * @return void
	 */
	public function __construct(
		private MaterialIn $model
	) {
	}

	private function setWorkingInstance(): void
	{
		$this->workingInstance = $this->retrieveWorkingInstance();
		$this->resetErrors();
	}

	private function retrieveWorkingInstance(): MaterialIn
	{
		if (Route::current() == null) {
			return $this->model;
		}

		$materialInId = Route::current()->parameter('material_in');

		if ($materialInId == null) {
			return $this->model;
		}

		$with = [
			'details.material',
			'details.outDetails',
			'details.stock'
		];

		return $this->model::with($with)->findOrFail($materialInId);
	}

	/**
	 * Insert new MaterialIn and MaterialInDetails to database
	 *
	 * @param array $data input from request
	 * @param array $detailsData input from request
	 * @return MaterialIn
	 */
	public function create(array $data, array $detailsData): MaterialIn
	{
		$this->setWorkingInstance();

		$this->validateData($data);
		$this->validateDetailsData($detailsData);


		if (!$this->getErrors()) {
			try {
				DB::beginTransaction();

				$this->workingInstance = $this->model::create($data);
				$this->addDataIdToDetails($detailsData);

				$this->workingInstance->details()->insert($detailsData);
			} catch (\Throwable $th) {
				DB::rollBack();
				$this->addError($th->getMessage());
			}
		}

		$this->throwErrorIfAny();

		DB::commit();

		return $this->workingInstance->fresh();
	}

	/**
	 * Update exists MaterialIn and MaterialInDetails in database
	 *
	 * @param array $data input from request
	 * @param array $detailsData input from request
	 * @return MaterialIn
	 **/
	public function update(array $data, array $detailsData): MaterialIn
	{
		$this->setWorkingInstance();

		$this->validateData($data);
		$this->validateDetailsData($detailsData);

		if ($this->getErrors()) {
			return $this->throwErrorIfAny();
		}

		[
			'forInsert' => $forInsert,
			'forUpdate' => $forUpdate,
			'forDelete' => $forDelete
		] = $this->separateDetailsData(collect($detailsData));


		try {
			DB::beginTransaction();

			// update material_in
			$this->workingInstance->update($data);

			$forUpsert = $forInsert->merge($forUpdate)->toArray();

			$this->addDataIdToDetails($forUpsert);

			// update/insert data from user input
			MaterialInDetail::upsert(
				$forUpsert,
				['material_in_id', 'material_id'],
				['qty', 'price']
			);

			// delete record that not exists in $detailsData
			$this->workingInstance->details()->whereIn('id', $forDelete->pluck('id')->toArray())->delete();

			DB::commit();
		} catch (\Throwable $th) {
			DB::rollBack();
			$this->addError($th->getMessage());
		}


		$this->throwErrorIfAny();


		return $this->workingInstance->fresh();
	}

	/**
	 * Delete exists MaterialIn and MaterialInDetails in database
	 *
	 * @return MaterialIn
	 **/
	public function deleteData(): MaterialIn
	{
		$this->setWorkingInstance();

		$details = $this->workingInstance->details;
		$isDetailsNotUsed = $this->filterValidDetailsForDelete($details)->count() === $details->count();

		if ($isDetailsNotUsed) {
			try {
				DB::beginTransaction();

				$this->workingInstance->details()->delete();
				$this->workingInstance->delete();

				DB::commit();
			} catch (\Throwable $th) {
				DB::rollBack();
				$this->addError($th->getMessage());
			}
		}

		$this->throwErrorIfAny();

		return $this->workingInstance;
	}
}
