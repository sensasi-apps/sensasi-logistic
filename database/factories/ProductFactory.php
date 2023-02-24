<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->numerify('###'),
            'name' => $this->faker->unique()->word(),
            'tags' => $this->faker->words(),
            'default_price' => $this->faker->randomNumber(2, true) . '000',
            'unit' => $this->faker->randomElement(['lt', 'ml', 'kg', 'g', 'box', 'pcs'])
        ];
    }
}
