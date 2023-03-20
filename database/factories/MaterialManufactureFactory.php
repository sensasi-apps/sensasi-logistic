<?php

namespace Database\Factories;

use App\Models\MaterialOut;
use App\Models\MaterialIn;
use Illuminate\Database\Eloquent\Factories\Factory;

class MaterialManufactureFactory extends Factory
{
    private array $usedMaterialOutIds = [];
    private array $usedMaterialInIds = [];

    private array $materialOutIds = [];
    private array $materialInIds = [];

    public function definition(): array
    {
        if (!$this->materialOutIds) {
            $this->materialOutIds = MaterialOut::doesntHave('productManufacture')->doesntHave('materialManufacture')->get()->pluck('id')->toArray();
        }

        if (!$this->materialInIds) {
            $this->materialInIds = MaterialIn::all()->pluck('id')->toArray();
        }

        do {
            $materialOutId = $this->faker->randomElement($this->materialOutIds);
        } while (in_array($materialOutId, $this->usedMaterialOutIds));

        do {
            $materialInId = $this->faker->randomElement($this->materialInIds);
        } while (in_array($materialInId, $this->usedMaterialInIds));

        array_push($this->usedMaterialOutIds, $materialOutId);
        array_push($this->usedMaterialInIds, $materialInId);

        return [
            'code' => $this->faker->unique()->numerify('MMAN-#####'),
            'at' => $this->faker->dateTimeBetween('-3 months', '-1 week'),
            'note' => $this->faker->sentence(10),
            'material_out_id' => $materialOutId,
            'material_in_id' => $materialInId
        ];
    }
}
