<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MaterialFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->numerify('###'),
            'name' => $this->faker->unique()->word(),
            'tags' => $this->faker->words(),
            'unit' => $this->faker->randomElement(['lt', 'ml', 'kg', 'g', 'box', 'pcs'])
        ];
    }
}
