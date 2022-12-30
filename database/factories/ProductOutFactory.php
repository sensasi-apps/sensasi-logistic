<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductOutFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $upperLimit = Carbon::now()->subMonths(3);

        return [
            'code' => $this->faker->unique()->numerify('P-OUT-#####'),
            'at' => $this->faker->dateTimeThisYear($upperLimit),
            'type' => $this->faker->randomElement([__('Stock Opname'), __('Return'), __('Sales'), __('Other')]),
            'note' => $this->faker->sentence(10)
        ];
    }
}
