<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MaterialOutDetailFactory extends Factory
{
    private $usedNumbers = [];

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $materialOutIds = \App\Models\MaterialOut::all()->pluck('id');
        $materialInDetailIds = \App\Models\MaterialInDetail::all()->pluck('id');
        
        do {
            $materialOutId = $this->faker->randomElement($materialOutIds);
            $materialInDetailId = $this->faker->randomElement($materialInDetailIds);

            $uniqueIds = $materialOutId . $materialInDetailId;
            
        } while (in_array($uniqueIds, $this->usedNumbers));

        array_push($this->usedNumbers, $uniqueIds);

        return [
            'material_out_id' => $materialOutId,
            'material_in_detail_id' => $materialInDetailId,
            'qty' => $this->faker->randomNumber(3, true)
        ];
    }
}
