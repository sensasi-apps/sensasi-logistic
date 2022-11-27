<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductInDetailsStockVew extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection('mysql')->statement('CREATE OR REPLACE
            VIEW product_in_details_stock_view AS
            SELECT
                pid.id as product_in_detail_id,
                pid.qty - COALESCE(SUM(`pod`.qty),0) as qty
            FROM product_in_details pid
            LEFT JOIN product_out_details `pod` ON `pod`.product_in_detail_id = pid.id
            GROUP BY pid.id, pid.qty
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
