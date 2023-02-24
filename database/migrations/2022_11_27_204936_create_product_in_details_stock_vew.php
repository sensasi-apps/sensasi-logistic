<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateProductInDetailsStockVew extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('CREATE
            OR REPLACE VIEW product_in_details_stock_view AS
            SELECT
                pid.id as product_in_detail_id,
                `pid`.`qty` - COALESCE(SUM(`pod`.`qty`), 0) as qty
            FROM
                product_in_details AS `pid`
                LEFT JOIN product_out_details AS `pod` ON pid.id = `pod`.product_in_detail_id
                LEFT JOIN product_outs AS `po` ON `pod`.product_out_id = `po`.id
                LEFT JOIN product_ins AS `pi` ON `pid`.product_in_id = `pi`.id
            GROUP BY
                pid.id, pid.qty;
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP VIEW product_in_details_stock_view');
    }
}
