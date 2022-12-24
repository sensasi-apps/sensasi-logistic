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
        $userIds = \App\Models\User::all()->pluck('id');
        $upperLimit = Carbon::now()->subMonths(3);

        return [
            'code' => $this->faker->unique()->numerify('P-IN-#####'),
            'at' => $this->faker->dateTimeThisYear($upperLimit),
            'type' => $this->faker->randomElement(['Pembelian', 'Hibah']),
            'created_by_user_id' => $this->faker->randomElement($userIds),
            'last_updated_by_user_id' => $this->faker->randomElement($userIds),
            'note' => $this->faker->sentence(10)
        ];
    }
}
