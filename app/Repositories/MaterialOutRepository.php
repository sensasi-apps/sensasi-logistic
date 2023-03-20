<?php

namespace App\Repositories;

use App\Models\MaterialOut;
use App\Models\MaterialOutDetail;
use App\Models\Views\MaterialInDetailsStockView;
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

	public function store(array $materialOut): MaterialOut
	{
		$validatedData = $this->validateData($materialOut);
		$validatedDetailsData = $this->validateDetailsData($materialOut['details']);

		try {
			DB::beginTransaction();

			$this->workingInstance = $this->workingInstance::create($validatedData);
			$this->addDataIdToArray($validatedDetailsData);

			$this->workingInstance->details()->insert($validatedDetailsData);
			DB::commit();
		} catch (\Throwable $th) {
			DB::rollBack();
			throw $th;
		}

		return $this->workingInstance->fresh();
	}

	public function update(array $materialOut): MaterialOut
	{
		$validatedData = $this->validateData($materialOut);
		$validatedDetails = $this->validateDetailsData($materialOut['details']);

		[
			'forInsert' => $forInsert,
			'forUpdate' => $forUpdate,
			'forDelete' => $forDelete
		] = $this->separateDetailsData($validatedDetails);

		try {
			DB::beginTransaction();

			$this->workingInstance->update($validatedData);

			$forUpsert = $forInsert->merge($forUpdate)->toArray();
			$this->addDataIdToArray($forUpsert);

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
		$this->validateDeleteData();

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

	// VALIDATION
	private function validateData(array $materialOut): array
	{
		return Validator::make($materialOut, [
			'code' => "nullable|string|unique:material_outs,code,{$this->workingInstance->id}",
			'type' => 'required|string',
			'note' => 'nullable|string',
			'at' => 'required|date'
		])->validate();
	}

	private function validateDetailsData(array $materialOutDetails): array
	{
		$materialInDetailsStock = MaterialInDetailsStockView::whereIn('material_in_detail_id', array_column($materialOutDetails, 'material_in_detail_id'))->get()->keyBy('material_in_detail_id');
		$existsDetails = $this->workingInstance->details->keyBy('material_in_detail_id');

		return Validator::make($materialOutDetails, [
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
					'distinct',
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
					'numeric',
					"max:{$max}",
				];
			}),
		])->validate();
	}

	private function validateDeleteData(): array
	{
		return Validator::make($this->workingInstance->toArray(), [
			'id' => "unique:product_manufactures,material_out_id,{$this->workingInstance->id},material_out_id|unique:material_manufactures,material_out_id,{$this->workingInstance->id},material_out_id"
		])->validate();
	}

	private function separateDetailsData(array $materialOutDetails): array
	{
		$existsMaterialOutDetails = $this->workingInstance->details;
		$existsMaterialInIds = $existsMaterialOutDetails->pluck('material_in_detail_id');

		$materialOutDetails = collect($materialOutDetails);
		$detailsDataMaterialInIds = $materialOutDetails->pluck('material_in_detail_id');

		$t1 = $detailsDataMaterialInIds->diff($existsMaterialInIds);
		$t2 = $detailsDataMaterialInIds->intersect($existsMaterialInIds);
		$t3 = $existsMaterialInIds->diff($detailsDataMaterialInIds);

		return [
			'forInsert' => $materialOutDetails->whereIn('material_in_detail_id', $t1->toArray()),
			'forUpdate' => $materialOutDetails->whereIn('material_in_detail_id', $t2->toArray()),
			'forDelete' => $existsMaterialOutDetails->whereIn('material_in_detail_id', $t3->toArray())
		];
	}
}
