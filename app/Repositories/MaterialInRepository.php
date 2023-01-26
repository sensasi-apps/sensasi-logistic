<?php

namespace App\Repositories;

use App\Models\MaterialIn;
use App\Models\MaterialInDetail;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MaterialInRepository
{
	private MaterialIn $workingInstance;
	private array $errors = [];

	/**
	 * Create a new repository instance.
	 *
	 * @return void
	 */
	public function __construct(
		private MaterialIn $model,
		private MaterialInDetail $detailModel
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
				if ($this->workingInstance = $this->model->create($data)) {
					foreach ($detailsData as &$detailData) {
						$detailData['material_in_id'] = $this->workingInstance->id;
					}

					$this->detailModel->insert($detailsData);
				}
			} catch (\Throwable $th) {
				DB::rollBack();
				$this->errors[] = $th->getMessage();
			}
		}

		$this->throwErrorIfAny();

		DB::commit();

		return $this->workingInstance;
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
		$this->setWorkingInstance($data['id']);

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
					$this->detailModel->upsert(
						$detailsData,
						['material_id', 'material_in_id'],
						['qty', 'price']
					);

					// delete record that not exists in $detailsData
					$this->detailModel->delete($forDelete->pluck('id')->toArray());
				}
			} catch (\Throwable $th) {
				DB::rollBack();
				$this->errors[] = $th->getMessage();
			}
		}

		$this->throwErrorIfAny();

		DB::commit();

		return $this->workingInstance;
	}

	/**
	 * Delete exists MaterialIn and MaterialInDetails in database
	 *
	 * @param int $id
	 * @return MaterialIn
	 **/
	public function delete(int $id): MaterialIn
	{
		$this->setWorkingInstance($id);

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

	/**
	 * Throw error if any
	 *
	 * @return void
	 * @throws \Exception
	 */
	private function throwErrorIfAny(): void
	{
		if ($this->errors) {
			throw new \Exception(json_encode($this->errors));
		}
	}

	/**
	 * set property working instance
	 * @param int $id
	 */
	private function setWorkingInstance(int $id): void
	{
		$this->workingInstance = $this->model->with('details.outDetails', 'details.stock')->findOrFail($id);
	}

	/**
	 * Validate MaterialIn input from user
	 *
	 * @param array $data input from request
	 * @return void
	 */
	private function validateData(array $data): void
	{
		$validator = Validator::make($data, [
			'id' => 'nullable|numeric',
			'code' => 'nullable|string|unique:material_ins,code' . (isset($data['id']) && $data['id'] ? ",{$data['id']},id" : null),
			'type' => 'required|string',
			'note' => 'nullable|string',
			'at' => 'required|date'
		]);


		if ($validator->fails()) {
			$this->errors = array_merge($this->errors, $validator->errors()->toArray());
		}
	}

	/**
	 * Validate details data
	 *
	 * @param array $detailsData input from request
	 * @return void
	 */
	private function validateDetailsData(array $detailsData): void
	{
		$validator = Validator::make($detailsData, [
			'*.material_id' => 'required|exists:materials,id',
			'*.qty' => 'required|integer',
			'*.price' => 'required|integer'
		]);

		if ($validator->fails()) {
			$this->errors = array_merge($this->errors, $validator->errors()->toArray());
		}
	}

	/**
	 * Separate details data for insert, update, and delete action
	 *
	 * @param Collection $detailsData
	 * @return array
	 */
	public function separateDetailsData(Collection $detailsData): array
	{
		$existsMaterialInDetails = $this->workingInstance->details->keyBy('material_id');
		$detailsDataCollection = $detailsData->keyBy('material_id');
		$existsMaterialIds = $existsMaterialInDetails->pluck('material_id');
		$detailsDataMaterialIds = $detailsDataCollection->pluck('material_id');

		// get material ids that not exists in old materials
		$t1 = $detailsDataMaterialIds->diff($existsMaterialIds);
		// get material ids that exists in old materials
		$t2 = $detailsDataMaterialIds->intersect($existsMaterialIds);
		// get material ids that not exists in new materials
		$t3 = $existsMaterialIds->diff($detailsDataMaterialIds);

		return [
			'forInsert' => $detailsDataCollection->whereIn('material_id', $t1->toArray()),
			'forUpdate' => $detailsDataCollection->whereIn('material_id', $t2->toArray()),
			'forDelete' => $existsMaterialInDetails->whereIn('material_id', $t3->toArray())
		];
	}

	public function validateDetailsDataForUpdate(Collection $detailsData): void
	{
		$existsMaterialInDetails = $this->workingInstance->details->keyBy('material_id');

		$detailsData->each(function ($detailData) use ($existsMaterialInDetails) {

			$materialInDetail = $existsMaterialInDetails->get($detailData['material_id']);

			$isStockEnoughForUpdate = $materialInDetail->qty_remain >= $materialInDetail->qty - $detailData['qty'];

			if (!$isStockEnoughForUpdate) {
				$this->errors[] = __('error.new qty is make stock negative', ['name' => $materialInDetail->material->name]);
			}
		});
	}

	public function validateDetailsDataForDelete(Collection $MaterialInDetails): void
	{
		$MaterialInDetails->each(function ($materialInDetail) {
			if (!$materialInDetail->outDetails) {
				$this->errors[] = __('error.delete.data is used', ['name' => $materialInDetail->material->name, 'type' => __('Material In')]);
			}
		});
	}
}
