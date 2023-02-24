<?php

namespace Database\Factories;

use App\Models\Material;
use App\Models\MaterialIn;
use Illuminate\Database\Eloquent\Factories\Factory;

class MaterialInDetailFactory extends Factory
{
    private array $usedCombinationIds = [];

    private array $materialIds = [];
    private array $materialInIds = [];

    public function definition(): array
    {
        if (!$this->materialIds) {
            $this->materialIds = Material::all()->pluck('id')->toArray();
        }

        if (!$this->materialInIds) {
            $this->materialInIds = MaterialIn::all()->pluck('id')->toArray();
        }

        do {
            $materialInId = $this->faker->randomElement($this->materialInIds);
            $materialId = $this->faker->randomElement($this->materialIds);

            $randomCombinationIds = "{$materialInId}-{$materialId}";
        } while (in_array($randomCombinationIds, $this->usedCombinationIds));

        array_push($this->usedCombinationIds, $randomCombinationIds);

        return [
            'material_in_id' => $materialInId,
            'material_id' => $materialId,
            'qty' => $this->faker->randomNumber(3, true),
            'price' => $this->faker->randomNumber(2, true) . '000'
        ];
    }
}
