<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {        
        if (\App\Models\User::where('name', 'superman')->count() == 0) {
            \App\Models\User::create([
                'name' => 'superman',
                'email' => 'super@man.com',
                'password' => bcrypt('superman')
            ])->assignRole('Super Admin');
        }

        \App\Models\User::factory(10)->create();
        \App\Models\Material::factory(25)->create();
        \App\Models\MaterialIn::factory(50)->create();
        \App\Models\MaterialInDetail::factory(300)->create();
        \App\Models\MaterialOut::factory(50)->create();
        \App\Models\MaterialOutDetail::factory(100)->create();

        $this->call([
            UserRoleSeeder::class
        ]);
    }
}
