<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MaterialOutFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->numerify('M-OUT-#####'),
            'at' => $this->faker->dateTimeBetween('-3 months', '-1 week'),
            'type' => $this->faker->randomElement([__('Stock Opname'), __('Return')]),
            'note' => $this->faker->sentence(10)
        ];
    }
}
