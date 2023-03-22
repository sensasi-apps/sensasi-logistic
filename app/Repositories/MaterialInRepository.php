<?php

namespace App\Repositories;

use App\Models\MaterialIn;
use App\Models\MaterialInDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MaterialInRepository extends BaseRepository
{
	protected string $urlParamName = 'material_in';
	protected string $modelClass = MaterialIn::class;

	protected array $withs = [
		'details.material',
		'details.outDetails',
		'details.stock'
	];

	public function store(array $materialIn): MaterialIn
	{
		$validatedData = $this->validateData($materialIn);
		$validatedDetailsData = $this->validateDetailsData($materialIn['details']);

		try {
			DB::beginTransaction();

			$this->workingInstance = $this->modelClass::create($validatedData);
			$this->addDataIdToArray($validatedDetailsData);

			$this->workingInstance->details()->insert($validatedDetailsData);
		} catch (\Throwable $th) {
			DB::rollBack();
			throw $th;
		}

		DB::commit();

		return $this->workingInstance;
	}

	public function update(array $materialIn): MaterialIn
	{
		$validatedData = $this->validateData($materialIn);
		$validatedDetails = $this->validateDetailsData($materialIn['details']);

		[
			'forInsert' => $forInsert,
			'forUpdate' => $forUpdate,
			'forDelete' => $forDelete
		] = $this->separateDetailsData($validatedDetails);

		$this->validateForDelete($forDelete->toArray());

		try {
			DB::beginTransaction();

			// update material_in
			$this->workingInstance->update($validatedData);

			// update/insert data from user input
			$forUpsert = $forInsert->merge($forUpdate)->toArray();
			$this->addDataIdToArray($forUpsert);
			MaterialInDetail::upsert(
				$forUpsert,
				['material_in_id', 'material_id'],
				['qty', 'price', 'manufactured_at', 'expired_at']
			);

			// delete record that not exists in $detailsData
			$this->workingInstance->details()->whereIn('id', $forDelete->pluck('id')->toArray())->delete();

			DB::commit();
		} catch (\Throwable $th) {
			DB::rollBack();
			throw $th;
		}

		return $this->workingInstance;
	}

	private function validateDeleteData(): array
	{
		return Validator::make($this->workingInstance->toArray(), [
			'id' => "unique:material_manufactures,material_in_id,{$this->workingInstance->id},material_in_id",
		])->validate();
	}

	public function destroy(): MaterialIn
	{
		$this->validateDeleteData();
		$this->validateForDelete($this->workingInstance->details->toArray());

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

	private function validateData(array $materialIn): array
	{
		return Validator::make($materialIn, [
			'code' => "nullable|string|unique:material_ins,code,{$this->workingInstance->id}",
			'type' => 'required|string',
			'note' => 'nullable|string',
			'at' => 'required|date'
		])->validate();
	}

	private function validateDetailsData(array $materiialInDetails): array
	{
		$existsDetails = $this->workingInstance->details->keyBy('material_id');

		return Validator::make($materiialInDetails, [
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
					'distinct',
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
					'numeric',
					"min:{$min}"
				];
			}),

			'*.price' => 'required|numeric|min:0',
			// TODO: implement check if expired_at is after at main: after_or_equal:{$productIn['at']}
			'*.expired_at' => "nullable|date",
			// TODO: implement check if manufactured_at is before at main: before_or_equal:{$productIn['at']}
			'*.manufactured_at' => "nullable|date"
		])->validate();
	}

	private function validateForDelete(array $materialInDetails): array
	{
		return Validator::make($materialInDetails, [
			'*.id' => 'unique:material_out_details,material_in_detail_id'
		])->validate();
	}

	private function separateDetailsData(array $materialInDetails): array
	{
		$existsMaterialInDetails = $this->workingInstance->details->keyBy('material_id');
		$existsMaterialIds = $existsMaterialInDetails->pluck('material_id');

		$materialInDetailsCollection = collect($materialInDetails)->keyBy('material_id');
		$materialInDetailsMaterialIds = $materialInDetailsCollection->pluck('material_id');

		$t1 = $materialInDetailsMaterialIds->diff($existsMaterialIds);
		$t2 = $materialInDetailsMaterialIds->intersect($existsMaterialIds);
		$t3 = $existsMaterialIds->diff($materialInDetailsMaterialIds);

		return [
			'forInsert' => $materialInDetailsCollection->whereIn('material_id', $t1->toArray()),
			'forUpdate' => $materialInDetailsCollection->whereIn('material_id', $t2->toArray()),
			'forDelete' => $existsMaterialInDetails->whereIn('material_id', $t3->toArray())
		];
	}
}
