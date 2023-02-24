<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = [
            [
                'code' => 'SP001',
                'brand' => 'Sirupku',
                'name' => 'Sirup Rasa Mangga 1L',
                'tags_json' => json_encode(['mango', 'sirup', 'beverage']),
                'default_price' => 10000,
                'unit' => 'botol'
            ],
            [
                'code' => 'SP002',
                'brand' => 'Sirupku',
                'name' => 'Sirup Rasa Jeruk 1L',
                'tags_json' => json_encode(['orange', 'sirup', 'beverage']),
                'default_price' => 10000,
                'unit' => 'botol'
            ],
            [
                'code' => 'SP003',
                'brand' => 'Sirupku',
                'name' => 'Sirup Rasa Melon 1L',
                'tags_json' => json_encode(['melon', 'sirup', 'beverage']),
                'default_price' => 10000,
                'unit' => 'botol'
            ],
            [
                'code' => 'SP004',
                'brand' => 'Sirup Rasa',
                'name' => 'Sirup Rasa Strawberry 1L',
                'tags_json' => json_encode(['strawberry', 'sirup', 'beverage']),
                'default_price' => 10000,
                'unit' => 'botol'
            ],
            [
                'code' => 'SP005',
                'brand' => 'Sirup Rasa',
                'name' => 'Sirup Rasa Apel 1L',
                'tags_json' => json_encode(['apple', 'sirup', 'beverage']),
                'default_price' => 10000,
                'unit' => 'botol'
            ]
        ];

        DB::table('products')->insert($products);
    }
}
