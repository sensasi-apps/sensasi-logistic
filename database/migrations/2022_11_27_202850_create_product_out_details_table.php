<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateProductOutDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql')->create('product_out_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_in_detail_id')
                ->constrained('product_in_details')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('product_out_id')
                ->constrained('product_outs')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->integer('qty');
            $table->integer('price');
            $table->unique(['product_in_detail_id', 'product_out_id'], 'product_out_details_unique');
        });

        DB::connection('mysql')->unprepared('CREATE PROCEDURE
            product_out_details__product_monthly_movements_procedure (
                IN productOutId int,
                IN productInDetailId int
            )
            BEGIN
                DECLARE year_at int;
                DECLARE month_at int;

                SELECT YEAR(`at`), MONTH(`at`) INTO year_at, month_at
                FROM product_outs
                WHERE id = productOutId;

                INSERT INTO
                    product_monthly_movements (product_id, year, month, `out`)
                SELECT
                    pid.product_id,
                    YEAR(po.at),
                    MONTH(po.at),
                    @total_qty := SUM(`pod`.qty)
                FROM product_out_details `pod`
                LEFT JOIN product_in_details as pid ON pid.id = `pod`.product_in_detail_id
                LEFT JOIN product_outs as po ON po.id = `pod`.product_out_id
                WHERE
                    `pod`.product_in_detail_id = productInDetailId AND
                    YEAR(po.at) = year_at AND
                    MONTH(po.at) = month_at 
                GROUP BY pid.product_id, YEAR(po.at), MONTH(po.at)
                ON DUPLICATE KEY UPDATE `out` = @total_qty;
            END;
        ');

        DB::connection('mysql')->unprepared('CREATE TRIGGER
            product_out_details_after_insert_trigger
                AFTER INSERT
                ON product_out_details
                FOR EACH ROW
            BEGIN
                CALL product_out_details__product_monthly_movements_procedure(NEW.product_out_id, NEW.product_in_detail_id);
            END;
        ');

        // DB::connection('mysql')->unprepared('CREATE TRIGGER
        //     product_out_details_before_update_trigger
        //         BEFORE UPDATE
        //         ON product_out_details
        //         FOR EACH ROW
        //     BEGIN
        //         CALL mod_stock_check_procedure(NEW.product_in_detail_id, NEW.qty - OLD.qty)
        //     END;
        // ');

        DB::connection('mysql')->unprepared('CREATE TRIGGER
            product_out_details_after_update_trigger
                AFTER UPDATE
                ON product_out_details
                FOR EACH ROW
            BEGIN
                IF NEW.qty <> OLD.qty THEN               
                    CALL product_out_details__product_monthly_movements_procedure(NEW.product_out_id, NEW.product_in_detail_id);
                END IF;
            END;
        ');

        DB::connection('mysql')->unprepared('CREATE
            TRIGGER product_out_details_after_delete_trigger
                AFTER DELETE
                ON product_out_details
                FOR EACH ROW
            BEGIN
                CALL product_out_details__product_monthly_movements_procedure(old.product_out_id, old.product_in_detail_id);
            END;
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql')->dropIfExists('product_out_details');
    }
}
