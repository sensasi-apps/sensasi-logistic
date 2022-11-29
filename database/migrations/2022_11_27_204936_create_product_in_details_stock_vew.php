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
        DB::connection('mysql')->statement('CREATE OR REPLACE VIEW product_in_details_stock_view AS
            SELECT
                pid.id as product_in_detail_id,
                COALESCE(SUM(IF(`pi`.`deleted_at` IS NULL, `pid`.`qty`, 0)),0) - COALESCE(SUM(IF(`mo`.deleted_at IS NULL, `mod`.`qty`, 0)),0) as qty
            FROM product_in_details AS `pid`
            LEFT JOIN product_out_details AS `mod` ON pid.id = `mod`.product_in_detail_id
            LEFT JOIN product_outs AS `mo` ON `mod`.product_out_id = `mo`.id
            LEFT JOIN product_ins AS `pi` ON `pid`.product_in_id = `pi`.id
            GROUP BY pid.id, pid.qty;
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
