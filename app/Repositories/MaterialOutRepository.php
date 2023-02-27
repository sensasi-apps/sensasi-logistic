<?php

namespace App\Repositories;

use App\Models\MaterialOut;
use App\Models\MaterialOutDetail;
use App\Models\Views\MaterialInDetailsStockView;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MaterialOutRepository extends BaseRepository
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

	public function store(array $data, array $detailsData): MaterialOut
	{
		$validatedData = $this->validateData($data);
		$validatedDetailsData = $this->validateDetailsData($detailsData);

		try {
			DB::beginTransaction();

			$this->workingInstance = $this->workingInstance::create($validatedData);
			$this->addDataIdToDetails($validatedDetailsData);

			$this->workingInstance->details()->insert($validatedDetailsData);
			DB::commit();
		} catch (\Throwable $th) {
			DB::rollBack();
			throw $th;
		}

		return $this->workingInstance->fresh();
	}

	public function update(array $data, array $detailsData): MaterialOut
	{
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

	private function validateData(array $data): array
	{
		return Validator::make($data, [
			'code' => "nullable|string|unique:material_outs,code,{$this->workingInstance->id}",
			'type' => 'required|string',
			'note' => 'nullable|string',
			'at' => 'required|date'
		])->validate();
	}

	private function validateDetailsData(array $detailsData): array
	{
		$materialInDetailsStock = MaterialInDetailsStockView::whereIn('material_in_detail_id', array_column($detailsData, 'material_in_detail_id'))->get()->keyBy('material_in_detail_id');
		$existsDetails = $this->workingInstance->details->keyBy('material_in_detail_id');

		return Validator::make($detailsData, [
			'*.material_in_detail_id' => Rule::forEach(function ($value) use ($existsDetails) {

				$rule = Rule::unique('material_out_details')->where(function ($query) use ($value) {
					return $query->where([
						'material_out_id' => $this->workingInstance->id,
						'material_in_detail_id' => $value
					]);
				});

				if ($existsDetails->get($value)) {
					$rule->ignore($existsDetails->get($value)->id);
				}

				return [
					'required',
					'exists:material_in_details,id',
					$rule
				];
			}),

			'*.qty' => Rule::forEach(function ($value, $attr, $item) use ($materialInDetailsStock, $existsDetails) {

				$index = explode('.', $attr)[0];
				$materialInDetailId = $item["{$index}.material_in_detail_id"];

				$max = $materialInDetailsStock->get($materialInDetailId)->qty;

				if ($existsDetails->get($materialInDetailId)) {
					$max += $existsDetails->get($materialInDetailId)->qty;
				}

				return [
					'required',
					'integer',
					"max:{$max}",
				];
			}),
		])->validate();
	}


	private function validateForDelete(array $data): array
	{
		return Validator::make($data, [
			'id' => "unique:manufactures,material_out_id,{$this->workingInstance->id},material_out_id"
		])->validate();
	}

	private function separateDetailsData(Collection $detailsData): array
	{
		$existsMaterialOutDetails = $this->workingInstance->details;
		$detailsDataCollection = $detailsData;
		$existsMaterialInIds = $existsMaterialOutDetails->pluck('material_in_detail_id');
		$detailsDataMaterialInIds = $detailsDataCollection->pluck('material_in_detail_id');

		// get material ids that not exists in old materials
		$t1 = $detailsDataMaterialInIds->diff($existsMaterialInIds);
		// get material ids that exists in old materials
		$t2 = $detailsDataMaterialInIds->intersect($existsMaterialInIds);
		// get material ids that not exists in new materials
		$t3 = $existsMaterialInIds->diff($detailsDataMaterialInIds);

		return [
			'forInsert' => $detailsDataCollection->whereIn('material_in_detail_id', $t1->toArray()),
			'forUpdate' => $detailsDataCollection->whereIn('material_in_detail_id', $t2->toArray()),
			'forDelete' => $existsMaterialOutDetails->whereIn('material_in_detail_id', $t3->toArray())
		];
	}

	private function addDataIdToDetails(array &$detailsData): void
	{
		foreach ($detailsData as &$detailData) {
			if (!$this->workingInstance->id) {
				$this->workingInstance->refresh();
			}

			$detailData['material_out_id'] = $this->workingInstance->id;
		}
	}
}
