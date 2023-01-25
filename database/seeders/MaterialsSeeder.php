<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MaterialsSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $materials = [
            [
                'name' => 'gula',
                'brand' => 'Brand A',
                'code' => 'S001',
                'unit' => 'kg',
                'tags_json' => json_encode(['sweetener', 'food ingredient', 'bahan sirup'])
            ],
            [
                'name' => 'gula',
                'brand' => 'Brand B',
                'code' => 'S002',
                'unit' => 'kg',
                'tags_json' => json_encode(['sweetener', 'food ingredient', 'bahan sirup'])
            ],
            [
                'name' => 'gula',
                'brand' => 'Brand C',
                'code' => 'S003',
                'unit' => 'kg',
                'tags_json' => json_encode(['sweetener', 'food ingredient', 'bahan sirup'])
            ],
            [
                'name' => 'air',
                'brand' => 'Brand D',
                'code' => 'W001',
                'unit' => 'liter',
                'tags_json' => json_encode(['drinking water', 'bahan sirup'])
            ],
            [
                'name' => 'air',
                'brand' => 'Brand E',
                'code' => 'W002',
                'unit' => 'liter',
                'tags_json' => json_encode(['drinking water', 'bahan sirup'])
            ],
            [
                'name' => 'ekstrak flavoring',
                'brand' => 'Brand F',
                'code' => 'F001',
                'unit' => 'liter',
                'tags_json' => json_encode(['flavoring', 'food ingredient', 'bahan sirup'])
            ],
            [
                'name' => 'ekstrak flavoring',
                'brand' => 'Brand G',
                'code' => 'F002',
                'unit' => 'liter',
                'tags_json' => json_encode(['flavoring', 'food ingredient', 'bahan sirup'])
            ],
            [
                'name' => 'pewarna makanan',
                'brand' => 'Brand H',
                'code' => 'C001',
                'unit' => 'gram',
                'tags_json' => json_encode(['food coloring', 'food ingredient', 'bahan sirup'])
            ],
            [
                'name' => 'pewarna makanan',
                'brand' => 'Brand I',
                'code' => 'C002',
                'unit' => 'gram',
                'tags_json' => json_encode(['food coloring', 'food ingredient', 'bahan sirup'])
            ],
            [
                'name' => 'larutan glukosa',
                'brand' => 'Brand J',
                'code' => 'G001',
                'unit' => 'liter',
                'tags_json' => json_encode(['glucose solution', 'food ingredient', 'bahan sirup'])
            ],
            [
                'name' => 'larutan glukosa',
                'brand' => 'Brand K',
                'code' => 'G002',
                'unit' => 'liter',
                'tags_json' => json_encode(['glucose solution', 'food ingredient', 'bahan sirup'])
            ]
        ];

        DB::table('materials')->insert($materials);
    }
}
