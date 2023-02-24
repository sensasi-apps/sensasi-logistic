<?php

namespace App\Repositories\Traits;

use App\Models\ProductOut;
use App\Models\Views\ProductInDetailsStockView;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

trait ProductOutTrait
{
	private ProductOut $workingInstance;

	private function validateData(array $data): array
	{
		return Validator::make($data, [
			'code' => "nullable|string|unique:product_outs,code,{$this->workingInstance->id}",
			'type' => 'required|string',
			'note' => 'nullable|string',
			'at' => 'required|date'
		])->validate();
	}

	private function validateDetailsData(array $detailsData): array
	{
		$existsDetails = $this->workingInstance->details->keyBy('product_in_detail_id');
		$productInDetailsStock = ProductInDetailsStockView::whereIn('product_in_detail_id', array_column($detailsData, 'product_in_detail_id'))->get()->keyBy('product_in_detail_id');

		return Validator::make($detailsData, [
			'*.product_in_detail_id' => Rule::forEach(function ($value) use ($existsDetails) {

				$rule = Rule::unique('product_out_details')->where(function ($query) use ($value) {
					return $query->where([
						'product_out_id' => $this->workingInstance->id,
						'product_in_detail_id' => $value
					]);
				});

				if ($existsDetails->get($value)) {
					$rule->ignore($existsDetails->get($value)->id);
				}

				return [
					'required',
					'exists:product_in_details,id',
					$rule
				];
			}),

			'*.price' => [
				'required',
				'numeric',
				'min:0'
			],

			'*.qty' => Rule::forEach(function ($value, $attr, $item) use ($productInDetailsStock, $existsDetails) {

				$index = explode('.', $attr)[0];
				$productInDetailId = $item["{$index}.product_in_detail_id"];

				$max = $productInDetailsStock->get($productInDetailId)->qty;

				if ($existsDetails->get($productInDetailId)) {
					$max += $existsDetails->get($productInDetailId)->qty;
				}

				return [
					'required',
					'integer',
					"max:{$max}",
				];
			}),
		])->validate();
	}

	private function separateDetailsData(Collection $detailsData): array
	{
		$existsProductOutDetails = $this->workingInstance->details;
		$detailsDataCollection = $detailsData;
		$existsProductInIds = $existsProductOutDetails->pluck('product_in_detail_id');
		$detailsDataProductInIds = $detailsDataCollection->pluck('product_in_detail_id');

		// get product ids that not exists in old products
		$t1 = $detailsDataProductInIds->diff($existsProductInIds);
		// get product ids that exists in old products
		$t2 = $detailsDataProductInIds->intersect($existsProductInIds);
		// get product ids that not exists in new products
		$t3 = $existsProductInIds->diff($detailsDataProductInIds);

		return [
			'forInsert' => $detailsDataCollection->whereIn('product_in_detail_id', $t1->toArray()),
			'forUpdate' => $detailsDataCollection->whereIn('product_in_detail_id', $t2->toArray()),
			'forDelete' => $existsProductOutDetails->whereIn('product_in_detail_id', $t3->toArray())
		];
	}

	private function addDataIdToDetails(array &$detailsData): void
	{
		foreach ($detailsData as &$detailData) {
			if (!$this->workingInstance->id) {
				$this->workingInstance->refresh();
			}

			$detailData['product_out_id'] = $this->workingInstance->id;
		}
	}
}
