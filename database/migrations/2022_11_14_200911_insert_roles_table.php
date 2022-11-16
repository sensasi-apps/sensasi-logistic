<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;


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
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Role::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
