<?php

namespace App\Repositories\Traits;

use App\Models\MaterialIn;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
trait MaterialInTrait
{
	private MaterialIn $workingInstance;

	/**
	 * Validate MaterialIn input from user
	 *
	 * @param array $data input from request
	 * @return array
	 */
	private function validateData(array $data): array
	{
		return Validator::make($data, [
			'id' => 'nullable|numeric',
			'code' => 'nullable|string|unique:material_ins,code' . ($this->workingInstance->id ? ",{$this->workingInstance->id}" : null),
			'type' => 'required|string',
			'note' => 'nullable|string',
			'at' => 'required|date'
		])->validate();
	}

	/**
	 * Validate details data
	 *
	 * @param array $detailsData input from request
	 * @return array
	 */
	private function validateDetailsData(array $detailsData): array
	{
		$existsDetails = $this->workingInstance->details->keyBy('material_id');

		return Validator::make($detailsData, [
			'*.material_id' => Rule::forEach(function ($value) use ($existsDetails) {

				$rule = Rule::unique('material_in_details')->where(function ($query) use ($value) {
					return $query->where([
						'material_in_id' => $this->workingInstance->id,
						'material_id' => $value
					]);
				});

				if ($existsDetails->get($value)) {
					$rule->ignore($existsDetails->get($value)->id);
				}

				return [
					'required',
					'exists:materials,id',
					$rule
				];
			}),

			'*.qty' => Rule::forEach(function ($value, $attr, $item) use ($existsDetails) {

				$index = explode('.', $attr)[0];
				$materialId = $item["{$index}.material_id"];
				$materialInDetail = $existsDetails->get($materialId);

				$min = $materialInDetail ? $materialInDetail->outDetails->sum('qty') : 0;

				return [
					'required',
					'integer',
					"min:{$min}"
				];
			}),

			'*.price' => 'required|integer|min:0'
		])->validate();
	}


	private function validateForDelete(array $detailsData): array
	{
		return Validator::make($detailsData, [
			'*.id' => 'unique:material_out_details,material_in_detail_id'
		])->validate();
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

	private function addDataIdToDetails(array &$detailsData): void
	{
		foreach ($detailsData as &$detailData) {
			if (!$this->workingInstance->id) {
				$this->workingInstance->refresh();
			}

			$detailData['material_in_id'] = $this->workingInstance->id;
		}
	}
}
