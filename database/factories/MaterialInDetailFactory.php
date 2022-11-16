<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MaterialInDetailFactory extends Factory
{
    private $usedNumbers = [];
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $materialIds = \App\Models\Material::all()->pluck('id');
        $materialInIds = \App\Models\MaterialIn::all()->pluck('id');
        
        
        do {
            $materialInId = $this->faker->randomElement($materialInIds);
            $materialId = $this->faker->randomElement($materialIds);

            $uniqueIds = $materialInId . $materialId;
            
        } while (in_array($uniqueIds, $this->usedNumbers));

        array_push($this->usedNumbers, $uniqueIds);

        return [
            'material_in_id' => $materialInId,
            'material_id' => $materialId,
            'qty' => $this->faker->randomNumber(3, true),
            'price' => $this->faker->numberBetween(10000, 100000)
        ];
    }
}
