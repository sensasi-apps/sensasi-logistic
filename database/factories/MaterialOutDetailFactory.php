<?php

namespace Database\Factories;

use App\Models\MaterialOut;
use App\Models\MaterialInDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

class MaterialOutDetailFactory extends Factory
{
    private array $usedCombinationIds = [];

    private array $materialOutIds = [];
    private array $materialInDetailIds = [];

    public function definition(): array
    {
        if (!$this->materialOutIds) {
            $this->materialOutIds = MaterialOut::all()->pluck('id')->toArray();
        }

        if (!$this->materialInDetailIds) {
            $this->materialInDetailIds = MaterialInDetail::all()->pluck('id')->toArray();
        }

        do {
            $materialOutId = $this->faker->randomElement($this->materialOutIds);
            $materialInDetailId = $this->faker->randomElement($this->materialInDetailIds);

            $randomCombinationId = "{$materialOutId}-{$materialInDetailId}";
        } while (in_array($randomCombinationId, $this->usedCombinationIds));

        array_push($this->usedCombinationIds, $randomCombinationId);

        return [
            'material_out_id' => $materialOutId,
            'material_in_detail_id' => $materialInDetailId,
            'qty' => $this->faker->randomNumber(3, true)
        ];
    }
}
