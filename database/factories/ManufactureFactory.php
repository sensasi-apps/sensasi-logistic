<?php

namespace Database\Factories;

use Carbon\Carbon;
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
        $upperLimit = Carbon::now()->subMonths(3);

        return [
            'code' => $this->faker->unique()->numerify('MAN-#####'),
            'at' => $this->faker->dateTimeThisYear($upperLimit),
            'material_out_id' => $this->faker->randomElement($materialOutIds),
            'product_in_id' => $this->faker->randomElement($productInIds)            
        ];
    }
}
