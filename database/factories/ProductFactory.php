<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
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
