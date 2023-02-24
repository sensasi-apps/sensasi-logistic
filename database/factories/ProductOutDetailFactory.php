<?php

namespace Database\Factories;

use App\Models\ProductInDetail;
use App\Models\ProductOut;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductOutDetailFactory extends Factory
{
    private array $usedCombinationIds = [];

    private array $productOutIds = [];
    private array $productInDetailIds = [];

    public function definition(): array
    {
        if (!$this->productOutIds) {
            $this->productOutIds = ProductOut::all()->pluck('id')->toArray();
        }

        if (!$this->productInDetailIds) {
            $this->productInDetailIds = ProductInDetail::all()->pluck('id')->toArray();
        }

        do {
            $productOutId = $this->faker->randomElement($this->productOutIds);
            $productInDetailId = $this->faker->randomElement($this->productInDetailIds);

            $randomCombinationId = "{$productOutId}-{$productInDetailId}";
        } while (in_array($randomCombinationId, $this->usedCombinationIds));

        array_push($this->usedCombinationIds, $randomCombinationId);

        return [
            'product_out_id' => $productOutId,
            'product_in_detail_id' => $productInDetailId,
            'qty' => $this->faker->randomNumber(3, true),
            'price' => $this->faker->randomNumber(2, true) . '000'
        ];
    }
}
