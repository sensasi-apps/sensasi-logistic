<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ManufactureFactory extends Factory
{
    private $usedMaterialOutIds = [];
    private $usedProductInIds = [];
    private $materialOutIds;
    private $productInIds;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        if (!$this->materialOutIds) {
            // TODO: do this cache for all factories
            $this->materialOutIds = \App\Models\MaterialOut::all()->pluck('id');
        }

        if (!$this->productInIds) {
            $this->productInIds = \App\Models\ProductIn::all()->pluck('id');
        }

        do {
            $materialOutId = $this->faker->randomElement($this->materialOutIds);
        } while (in_array($materialOutId, $this->usedMaterialOutIds));

        do {
            $productInId = $this->faker->randomElement($this->productInIds);
        } while (in_array($productInId, $this->usedProductInIds));

        array_push($this->usedMaterialOutIds, $materialOutId);
        array_push($this->usedProductInIds, $productInId);

        return [
            'code' => $this->faker->unique()->numerify('MAN-#####'),
            'at' => $this->faker->dateTimeBetween('-3 months', '-1 week'),
            'material_out_id' => $materialOutId,
            'product_in_id' => $productInId
        ];
    }
}
