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
        DB::connection('mysql')->statement('CREATE
            OR REPLACE VIEW product_in_details_stock_view AS
            SELECT
                pid.id as product_in_detail_id,
                -- qty in - qty out
                COALESCE(
                    IF(`pi`.`deleted_at` IS NULL, `pid`.`qty`, 0),
                    0
                ) - COALESCE(
                    SUM(IF(`po`.deleted_at IS NULL, `pod`.`qty`, 0)),
                    0
                ) as qty
            FROM
                product_in_details AS `pid`
                LEFT JOIN product_out_details AS `pod` ON pid.id = `pod`.product_in_detail_id
                LEFT JOIN product_outs AS `po` ON `pod`.product_out_id = `po`.id
                LEFT JOIN product_ins AS `pi` ON `pid`.product_in_id = `pi`.id
            GROUP BY
                pid.id, pid.qty, pi.deleted_at;
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::connection('mysql')->statement('DROP VIEW product_in_details_stock_view');
    }
}
