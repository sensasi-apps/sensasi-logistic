<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class SpatiePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create(['name' => 'Super Admin']);

        $stackholderRole = Role::create(['name' => 'Stackholder']);
        $manufactureRole = Role::create(['name' => 'Manufacture']);
        $salesRole = Role::create(['name' => 'Sales']);
        $warehouseRole = Role::create(['name' => 'Warehouse']);
    }
}
