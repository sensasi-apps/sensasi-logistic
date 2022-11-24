<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MaterialOutFactory extends Factory
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
            'at' => $this->faker->dateTimeThisYear(),
            'type' => $this->faker->randomElement([__('Stock Opname'), __('Return')]),
            'created_by_user_id' => $this->faker->randomElement($userIds),
            'last_updated_by_user_id' => $this->faker->randomElement($userIds),
            'note' => $this->faker->sentence(10),
            'desc' => $this->faker->sentence(4)
        ];
    }
}
