<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;

class InsertRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Role::create(['name' => 'Super Admin']);

        $stackholderRole = Role::create(['name' => 'Stackholder']);
        $manufactureRole = Role::create(['name' => 'Manufacture']);
        $salesRole = Role::create(['name' => 'Sales']);
        $warehouseRole = Role::create(['name' => 'Warehouse']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Role::all()->delete();
    }
}
