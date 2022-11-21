<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateMaterialInDetailsStockView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection('mysql')->statement('CREATE OR REPLACE
            VIEW material_in_details_stock_view AS
            SELECT
                mid.id as material_in_detail_id,
                mid.qty - SUM(`mod`.qty) as qty
            FROM material_out_details `mod`
            LEFT JOIN material_in_details mid ON mid.id = `mod`.material_in_detail_id
            GROUP BY mid.id, mid.qty
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::connection('mysql')->statement('DROP VIEW material_in_details_stock_view');
    }
}
