<?php

namespace App\Repositories;

use App\Models\MaterialIn;
use App\Repositories\Traits\MaterialInTrait;
use Illuminate\Support\Facades\DB;

class MaterialInRepository extends BaseRepository
{
	use MaterialInTrait;

	/**
	 * Create a new repository instance.
	 *
	 * @return void
	 */
	public function __construct(
		private MaterialIn $workingInstance
	) {
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
		$this->validateData($data);
		$this->validateDetailsData($detailsData);

		if (!$this->errors) {
			try {
				DB::beginTransaction();
				if ($this->workingInstance->create($data)) {
					foreach ($detailsData as &$detailData) {
						$detailData['material_in_id'] = $this->workingInstance->id;
					}

					$this->workingInstance->detail()->insert($detailsData);
				}
			} catch (\Throwable $th) {
				DB::rollBack();
				$this->errors[] = $th->getMessage();
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
		$this->validateData($data);
		$this->validateDetailsData($detailsData);

		[
			'forUpdate' => $forUpdate,
			'forDelete' => $forDelete
		] = $this->separateDetailsData(collect($detailsData));

		$this->validateDetailsDataForUpdate($forUpdate);
		$this->validateDetailsDataForDelete($forDelete);

		if (!$this->errors) {
			try {
				DB::beginTransaction();
				if ($this->workingInstance->update($data)) {

					// set material_in_id for each detailData from user input
					foreach ($detailsData as &$detailData) {
						$detailData['material_in_id'] = $this->workingInstance->id;
					}

					// update/insert data from user input
					\App\Models\MaterialInDetail::upsert(
						$detailsData,
						['material_id', 'material_in_id'],
						['qty', 'price']
					);

					// delete record that not exists in $detailsData
					$this->workingInstance->details()
						->whereIn('material_in_id', $forDelete->pluck('id')->toArray())
						->delete();
				}
			} catch (\Throwable $th) {
				DB::rollBack();
				$this->errors[] = $th->getMessage();
			}
		}

		$this->throwErrorIfAny();

		DB::commit();

		return $this->workingInstance->fresh();
	}

	/**
	 * Delete exists MaterialIn and MaterialInDetails in database
	 *
	 * @return MaterialIn
	 **/
	public function deleteData(): MaterialIn
	{
		$this->validateDetailsDataForDelete($this->workingInstance->details);

		if (!$this->errors) {
			try {
				DB::beginTransaction();
				$this->workingInstance->delete();
			} catch (\Throwable $th) {
				DB::rollBack();
				$this->errors[] = $th->getMessage();
			}
		}

		$this->throwErrorIfAny();

		DB::commit();

		return $this->workingInstance;
	}
}
