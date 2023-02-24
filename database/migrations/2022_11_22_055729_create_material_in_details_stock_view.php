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
        DB::statement('CREATE
            OR REPLACE VIEW material_in_details_stock_view AS
            SELECT
                mid.id as material_in_detail_id,
                `mid`.`qty` - COALESCE(SUM(`mod`.`qty`), 0) as qty
            FROM
                material_in_details AS `mid`
                LEFT JOIN material_out_details AS `mod` ON mid.id = `mod`.material_in_detail_id
                LEFT JOIN material_outs AS `mo` ON `mod`.material_out_id = `mo`.id
                LEFT JOIN material_ins AS `mi` ON `mid`.material_in_id = `mi`.id
            GROUP BY
                mid.id, mid.qty;
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP VIEW material_in_details_stock_view');
    }
}
