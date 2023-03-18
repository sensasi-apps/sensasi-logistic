<?php

namespace Database\Factories;

use App\Models\MaterialOut;
use App\Models\ProductIn;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductManufactureFactory extends Factory
{
    private array $usedMaterialOutIds = [];
    private array $usedProductInIds = [];

    private array $materialOutIds = [];
    private array $productInIds = [];

    public function definition(): array
    {
        if (!$this->materialOutIds) {
            $this->materialOutIds = MaterialOut::all()->pluck('id')->toArray();
        }

        if (!$this->productInIds) {
            $this->productInIds = ProductIn::all()->pluck('id')->toArray();
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
            'note' => $this->faker->sentence(10),
            'material_out_id' => $materialOutId,
            'product_in_id' => $productInId
        ];
    }
}
