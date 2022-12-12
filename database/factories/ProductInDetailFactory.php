<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductInDetailFactory extends Factory
{
    private $usedNumbers = [];

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $productIds = \App\Models\Product::all()->pluck('id');
        $productInIds = \App\Models\ProductIn::all()->pluck('id');


        do {
            $productInId = $this->faker->randomElement($productInIds);
            $productId = $this->faker->randomElement($productIds);

            $uniqueIds = $productInId . $productId;
        } while (in_array($uniqueIds, $this->usedNumbers));

        array_push($this->usedNumbers, $uniqueIds);

        return [
            'product_in_id' => $productInId,
            'product_id' => $productId,
            'qty' => $this->faker->randomNumber(3, true)
        ];
    }
}
