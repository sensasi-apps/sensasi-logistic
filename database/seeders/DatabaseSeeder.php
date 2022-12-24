<?php

namespace Database\Seeders;

use App\Models\MaterialIn;
use App\Models\MaterialInDetail;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'superman',
            'email' => 'super@man.com',
            'password' => bcrypt('superman')
        ]);

        $user = \App\Models\User::where('email', 'super@man.com')
            ->first()
            ->assignRole('Super Admin');

        Auth::login($user);

        \App\Models\User::factory(10)->create();

        $this->call([
            UserRoleSeeder::class,
            MaterialsSeeder::class,
            ProductsSeeder::class,
        ]);

        echo "MaterialIn Factory is working...\n";
        \App\Models\MaterialIn::factory(50)->create();

        echo "MaterialInDetail Factory is working...\n";
        \App\Models\MaterialInDetail::factory(100)->create();

        echo "MaterialOut Factory is working...\n";
        \App\Models\MaterialOut::factory(59)->create();

        echo "MaterialOutDetail Factory is working...\n";
        \App\Models\MaterialOutDetail::factory(100)->create();

        echo "ProductIn Factory is working...\n";
        \App\Models\ProductIn::factory(50)->create();

        echo "ProductInDetail Factory is working...\n";
        \App\Models\ProductInDetail::factory(100)->create();

        echo "ProductOut Factory is working...\n";
        \App\Models\ProductOut::factory(50)->create();

        echo "ProductOutDetail Factory is working...\n";
        \App\Models\ProductOutDetail::factory(100)->create();

        echo "Manufacture Factory is working...\n";
        \App\Models\Manufacture::factory(50)->create();
    }
}
