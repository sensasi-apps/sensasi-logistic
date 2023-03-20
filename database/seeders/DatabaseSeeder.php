<?php

namespace Database\Seeders;

use App\Models\MaterialIn;
use App\Models\MaterialInDetail;
use App\Models\MaterialManufacture;
use App\Models\MaterialOut;
use App\Models\MaterialOutDetail;
use App\Models\ProductIn;
use App\Models\ProductInDetail;
use App\Models\ProductManufacture;
use App\Models\ProductOut;
use App\Models\ProductOutDetail;
use App\Models\User;
use Helper;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = Helper::createSuperman();
        Auth::login($user);

        User::factory(10)->create();

        $this->call([
            UserRoleSeeder::class,
            MaterialsSeeder::class,
            ProductsSeeder::class,
        ]);

        echo "MaterialIn Factory is working...\n";
        MaterialIn::factory(50)->create();

        echo "MaterialInDetail Factory is working...\n";
        MaterialInDetail::factory(100)->create();

        echo "MaterialOut Factory is working...\n";
        MaterialOut::factory(50)->create();

        echo "MaterialOutDetail Factory is working...\n";
        MaterialOutDetail::factory(100)->create();

        echo "ProductIn Factory is working...\n";
        ProductIn::factory(50)->create();

        echo "ProductInDetail Factory is working...\n";
        ProductInDetail::factory(100)->create();

        echo "ProductOut Factory is working...\n";
        ProductOut::factory(50)->create();

        echo "ProductOutDetail Factory is working...\n";
        ProductOutDetail::factory(100)->create();

        echo "ProductManufacture Factory is working...\n";
        ProductManufacture::factory(10)->create();

        echo "MaterialManufacture factory is working...\n";
        MaterialManufacture::factory(10)->create();
    }
}
