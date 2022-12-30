<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductInFactory extends Factory
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
            'code' => $this->faker->unique()->numerify('P-IN-#####'),
            'at' => $this->faker->dateTimeThisYear($upperLimit),
            'type' => $this->faker->randomElement(['Pembelian', 'Hibah']),
            'note' => $this->faker->sentence(10)
        ];
    }
}
