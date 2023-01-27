<?php

namespace App\Repositories\Traits;

use App\Models\MaterialIn;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Collection;

trait MaterialInTrait
{
	private MaterialIn $workingInstance;

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
			'code' => 'nullable|string|unique:material_ins,code' . ($this->workingInstance->id ? ",{$this->workingInstance->id}" : null),
			'type' => 'required|string',
			'note' => 'nullable|string',
			'at' => 'required|date'
		]);


		if ($validator->fails()) {
			$this->addError($validator->errors()->toArray());
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
			$this->addError($validator->errors()->toArray());
		}
	}

	/**
	 * Separate details data for insert, update, and delete action
	 *
	 * @param Collection $detailsData
	 * @return array
	 */
	private function separateDetailsData(Collection $detailsData): array
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

	private function validateDetailsDataForUpdate(Collection $detailsData): void
	{
		$existsMaterialInDetails = $this->workingInstance->details->keyBy('material_id');

		$detailsData->each(function ($detailData) use ($existsMaterialInDetails) {

			$materialInDetail = $existsMaterialInDetails->get($detailData['material_id']);

			$isStockEnoughForUpdate = $materialInDetail->qty_remain >= $materialInDetail->qty - $detailData['qty'];

			if (!$isStockEnoughForUpdate) {
				$material = $materialInDetail->material;
				$this->addError(__('error.new qty is make stock negative', ['name' => "{$material->name} ({$material->brand})", 'type' => '']));
			}
		});
	}

	private function validateDetailsDataForDelete(Collection $MaterialInDetails): void
	{
		$MaterialInDetails->each(function ($materialInDetail) {
			if ($materialInDetail->outDetails != null) {
				$material = $materialInDetail->material;
				$this->addError(__('error.delete.data is used', ['name' => "{$material->name} ({$material->brand})", 'type' => '']));
			}
		});
	}
}
