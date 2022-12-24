<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductOutDetailFactory extends Factory
{
    private $usedNumbers = [];

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $productOutIds = \App\Models\ProductOut::all()->pluck('id');
        $productInDetailIds = \App\Models\ProductInDetail::all()->pluck('id');

        do {
            $productOutId = $this->faker->randomElement($productOutIds);
            $productInDetailId = $this->faker->randomElement($productInDetailIds);

            $uniqueIds = $productOutId . $productInDetailId;
        } while (in_array($uniqueIds, $this->usedNumbers));

        array_push($this->usedNumbers, $uniqueIds);

        return [
            'product_out_id' => $productOutId,
            'product_in_detail_id' => $productInDetailId,
            'qty' => $this->faker->randomNumber(3, true),
            'price' => $this->faker->randomNumber(2, true) . '000'
        ];
    }
}
