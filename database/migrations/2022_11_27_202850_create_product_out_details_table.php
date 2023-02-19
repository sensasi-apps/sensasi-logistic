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
        Schema::create('product_out_details', function (Blueprint $table) {
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

        DB::unprepared('CREATE OR REPLACE PROCEDURE product_monthly_movements_upsert_out_procedure (
            IN productID int,
            IN yearAt int,
            IN monthAt int
        )
        BEGIN
                INSERT INTO
                    product_monthly_movements (product_id, year, month, `out`, avg_out, avg_price)
                SELECT
                    product_id,
                    yearAt,
                    monthAt,
                    @total_qty := SUM(qty),
                    @avg_qty := AVG(qty),
                    @avg_price := AVG(CASE WHEN price > 0 THEN price ELSE NULL END)
                FROM (SELECT productID as product_id, pod.qty, price
                    FROM product_in_details AS pid
                    LEFT JOIN product_out_details AS pod ON pid.id = pod.product_in_detail_id
                    LEFT JOIN product_outs AS po ON pod.product_out_id = po.id
                    WHERE
                        pid.product_id = productID AND
                        YEAR(po.at) = yearAt AND
                        MONTH(po.at) = monthAt AND
                        pod.qty > 0
                    UNION SELECT productID, 0, 0) AS qty_temp
                GROUP BY product_id
                ON DUPLICATE KEY UPDATE `out` = @total_qty, avg_out = @avg_qty, avg_price = @avg_price;
            END;
        ');

        DB::unprepared('CREATE OR REPLACE PROCEDURE product_out_details__product_monthly_movements_procedure (
                IN productOutId int,
                IN productInDetailId int
            )
            BEGIN
                DECLARE yearAt int;
                DECLARE monthAt int;
                DECLARE productID int;

                SELECT YEAR(`at`), MONTH(`at`) INTO yearAt, monthAt
                FROM product_outs
                WHERE id = productOutId;

                SELECT `pid`.`product_id` INTO productID
                FROM product_in_details AS `pid`
                WHERE id = productInDetailId;

                CALL product_monthly_movements_upsert_out_procedure(
                    productID,
                    yearAt,
                    monthAt
                );
            END;
        ');

        DB::unprepared('CREATE OR REPLACE TRIGGER product_outs_after_update_trigger
            AFTER UPDATE
            ON product_outs
            FOR EACH ROW
            BEGIN
                -- TODO: fix this like material_in_details

                IF YEAR(NEW.at) <> YEAR(OLD.at) OR MONTH(NEW.at) <> MONTH(OLD.at) THEN
                    CALL product_monthly_movements_upsert_out_procedure(
                        (
                            SELECT product_in_detail_id
                            FROM product_out_details
                            WHERE product_out_id = OLD.id
                        ),
                        YEAR(OLD.at),
                        MONTH(OLD.at)
                    );

                    CALL product_out_details__product_monthly_movements_procedure(
                        OLD.id,
                        (
                            SELECT product_in_detail_id
                            FROM product_out_details
                            WHERE product_out_id = OLD.id
                        )
                    );
                END IF;
            END;
        ');

        DB::unprepared('CREATE OR REPLACE TRIGGER product_out_details_after_insert_trigger
                AFTER INSERT
                ON product_out_details
                FOR EACH ROW
            BEGIN
                CALL product_out_details__product_monthly_movements_procedure(NEW.product_out_id, NEW.product_in_detail_id);
            END;
        ');

        DB::unprepared('CREATE OR REPLACE TRIGGER product_out_details_after_update_trigger
                AFTER UPDATE
                ON product_out_details
                FOR EACH ROW
            BEGIN
                IF NEW.qty <> OLD.qty AND NEW.product_in_detail_id = OLD.product_in_detail_id THEN
                    CALL product_out_details__product_monthly_movements_procedure(NEW.product_out_id, NEW.product_in_detail_id);
                END IF;

                IF NEW.product_in_detail_id <> OLD.product_in_detail_id THEN
                    CALL product_out_details__product_monthly_movements_procedure(NEW.product_out_id, OLD.product_in_detail_id);
                    CALL product_out_details__product_monthly_movements_procedure(NEW.product_out_id, NEW.product_in_detail_id);
                END IF;
            END;
        ');

        DB::unprepared('CREATE OR REPLACE TRIGGER product_out_details_after_delete_trigger
                AFTER DELETE
                ON product_out_details
                FOR EACH ROW
            BEGIN
                CALL product_out_details__product_monthly_movements_procedure(OLD.product_out_id, OLD.product_in_detail_id);

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
        Schema::dropIfExists('product_out_details');
        DB::unprepared('DROP PROCEDURE IF EXISTS `product_monthly_movements_upsert_out_procedure`');
        DB::unprepared('DROP PROCEDURE IF EXISTS `product_out_details__product_monthly_movements_procedure`');
        DB::unprepared('DROP TRIGGER IF EXISTS product_outs_after_update_trigger');
        DB::unprepared('DROP TRIGGER IF EXISTS product_out_details_after_insert_trigger');
        DB::unprepared('DROP TRIGGER IF EXISTS product_out_details_after_update_trigger');
        DB::unprepared('DROP TRIGGER IF EXISTS product_out_details_after_delete_trigger');
    }
}
