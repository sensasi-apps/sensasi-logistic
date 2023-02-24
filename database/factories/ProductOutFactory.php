<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductOutFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->numerify('P-OUT-#####'),
            'at' => $this->faker->dateTimeBetween('-3 months', '-1 week'),
            'type' => $this->faker->randomElement([__('Stock Opname'), __('Return'), __('Sales'), __('Other')]),
            'note' => $this->faker->sentence(10)
        ];
    }
}
