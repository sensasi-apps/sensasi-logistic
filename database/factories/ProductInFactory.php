<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductInFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->numerify('P-IN-#####'),
            'at' => $this->faker->dateTimeBetween('-3 months', '-1 week'),
            'type' => $this->faker->randomElement(['Pembelian', 'Hibah']),
            'note' => $this->faker->sentence(10)
        ];
    }
}
