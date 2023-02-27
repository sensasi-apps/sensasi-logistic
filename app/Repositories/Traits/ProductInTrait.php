<?php

namespace App\Repositories\Traits;

use App\Models\ProductIn;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

trait ProductInTrait
{
	private ProductIn $workingInstance;

	private function validateData(array $data): array
	{
		return Validator::make($data, [
			'code' => "nullable|string|unique:product_ins,code,{$this->workingInstance->id}",
			'type' => 'required|string',
			'note' => 'nullable|string',
			'at' => 'required|date'
		])->validate();
	}

	private function validateDetailsData(array $detailsData): array
	{
		$existsDetails = $this->workingInstance->details->keyBy('product_id');

		return Validator::make($detailsData, [
			'*.product_id' => Rule::forEach(function ($value) use ($existsDetails) {

				$rule = Rule::unique('product_in_details')->where(function ($query) use ($value) {
					return $query->where([
						'product_in_id' => $this->workingInstance->id,
						'product_id' => $value
					]);
				});

				if ($existsDetails->get($value)) {
					$rule->ignore($existsDetails->get($value)->id);
				}

				return [
					'required',
					'distinct',
					'exists:products,id',
					$rule
				];
			}),

			'*.qty' => Rule::forEach(function ($value, $attr, $item) use ($existsDetails) {

				$index = explode('.', $attr)[0];
				$productId = $item["{$index}.product_id"];
				$productInDetail = $existsDetails->get($productId);

				$min = $productInDetail ? $productInDetail->outDetails->sum('qty') : 0;

				return [
					'required',
					'numeric',
					"min:{$min}"
				];
			}),

			'*.price' => 'required|numeric|min:0'
		])->validate();
	}


	private function validateForDelete(array $data, array $detailsData): array
	{
		return [
			Validator::make($data, [
				'id' => "unique:manufactures,product_in_id,{$this->workingInstance->id},product_in_id",
			])->validate(),
			Validator::make($detailsData, [
				'*.id' => 'unique:product_out_details,product_in_detail_id',
			])->validate()
		];
	}

	private function separateDetailsData(Collection $detailsData): array
	{
		$existsProductInDetails = $this->workingInstance->details->keyBy('product_id');
		$detailsDataCollection = $detailsData->keyBy('product_id');
		$existsProductIds = $existsProductInDetails->pluck('product_id');
		$detailsDataProductIds = $detailsDataCollection->pluck('product_id');

		// get product ids that not exists in old products
		$t1 = $detailsDataProductIds->diff($existsProductIds);
		// get product ids that exists in old products
		$t2 = $detailsDataProductIds->intersect($existsProductIds);
		// get product ids that not exists in new products
		$t3 = $existsProductIds->diff($detailsDataProductIds);

		return [
			'forInsert' => $detailsDataCollection->whereIn('product_id', $t1->toArray()),
			'forUpdate' => $detailsDataCollection->whereIn('product_id', $t2->toArray()),
			'forDelete' => $existsProductInDetails->whereIn('product_id', $t3->toArray())
		];
	}

	private function addDataIdToDetails(array &$detailsData): void
	{
		foreach ($detailsData as &$detailData) {
			if (!$this->workingInstance->id) {
				$this->workingInstance->refresh();
			}

			$detailData['product_in_id'] = $this->workingInstance->id;
		}
	}
}
