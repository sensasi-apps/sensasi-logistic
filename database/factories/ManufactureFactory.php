<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ManufactureFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $productInIds = \App\Models\ProductIn::all()->pluck('id');
        $materialOutIds = \App\Models\MaterialOut::all()->pluck('id');

        return [
            'code' => $this->faker->unique()->numerify('MAN-#####'),
            'at' => $this->faker->dateTimeBetween('-3 months', '-1 week'),
            'material_out_id' => $this->faker->randomElement($materialOutIds),
            'product_in_id' => $this->faker->randomElement($productInIds)            
        ];
    }
}
