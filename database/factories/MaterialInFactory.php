<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MaterialInFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $userIds = \App\Models\User::all()->pluck('id');
        return [
            'code' => $this->faker->unique()->numerify('#####'),
            'at' => $this->faker->dateTimeThisYear('+2 months'),
            'type' => $this->faker->randomElement(['Pembelian', 'Hibah']),
            'created_by_user_id' => $this->faker->randomElement($userIds),
            'last_updated_by_user_id' => $this->faker->randomElement($userIds),
            'note' => $this->faker->sentence(10),
            'desc' => $this->faker->sentence(4)
        ];
    }
}
