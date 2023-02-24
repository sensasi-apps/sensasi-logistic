<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductIn;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductInDetailFactory extends Factory
{
    private array $usedCombinationIds = [];

    private array $productInIds = [];
    private array $productIds = [];

    public function definition(): array
    {
        if (!$this->productInIds) {
            $this->productInIds = ProductIn::all()->pluck('id')->toArray();
        }

        if (!$this->productIds) {
            $this->productIds = Product::all()->pluck('id')->toArray();
        }

        do {
            $productInId = $this->faker->randomElement($this->productInIds);
            $productId = $this->faker->randomElement($this->productIds);

            $randomCombinationId = $productInId . $productId;
        } while (in_array($randomCombinationId, $this->usedCombinationIds));

        array_push($this->usedCombinationIds, $randomCombinationId);

        return [
            'product_in_id' => $productInId,
            'product_id' => $productId,
            'qty' => $this->faker->randomNumber(3, true),
            'price' => $this->faker->randomNumber(2, true) . '000'
        ];
    }
}
