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
        Role::create(['name' => 'Admin']);
        Role::create(['name' => 'Stackholder']);
        Role::create(['name' => 'Warehouse']);
        Role::create(['name' => 'Sales']);
        Role::create(['name' => 'Purchase']);
        Role::create(['name' => 'Manufacture']);
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
