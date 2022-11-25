<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateProductInDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql')->create('product_in_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_in_id')
            ->constrained('product_ins')
            ->cascadeOnUpdate()
            ->restrictOnDelete();

            $table->foreignId('product_id')
            ->constrained('products')
            ->cascadeOnUpdate()
            ->restrictOnDelete();

            $table->integer('qty');
            $table->unique(['product_id','product_in_id']);
        });

  
        DB::connection('mysql')->unprepared('
            CREATE PROCEDURE product_in_details__product_monthly_movements_procedure (
                IN productInID int,
                IN productID int
            )
            BEGIN
                INSERT INTO
                    product_monthly_movements (product_id, year, month, `in`)
                SELECT
                    pid.product_id,
                    YEAR(pi.at),
                    MONTH(pi.at),
                    @total_qty := SUM(pid.qty)
                FROM product_in_details pid
                LEFT JOIN product_ins as pi ON pi.id = productInID
                WHERE
                    pid.product_id = productID
                GROUP BY pid.product_id, YEAR(pi.at), MONTH(pi.at)
                ON DUPLICATE KEY UPDATE `in` = @total_qty;
            END;
        ');

        DB::connection('mysql')->unprepared('
            CREATE TRIGGER product_in_details_after_insert_trigger
                AFTER INSERT
                ON product_in_details
                FOR EACH ROW
            BEGIN
                CALL product_in_details__product_monthly_movements_procedure(NEW.product_in_id, NEW.product_id);
            END;
        ');

        DB::connection('mysql')->unprepared('
            CREATE TRIGGER product_in_details_after_update_trigger
                AFTER UPDATE
                ON product_in_details
                FOR EACH ROW
            BEGIN
                IF NEW.qty <> OLD.qty THEN               
                    CALL product_in_details__product_monthly_movements_procedure(NEW.product_in_id, NEW.product_id);
                END IF;
            END;
        ');

        DB::connection('mysql')->unprepared('
            CREATE TRIGGER product_in_details_after_delete_trigger
                AFTER DELETE
                ON product_in_details
                FOR EACH ROW
            BEGIN
                CALL product_in_details__product_monthly_movements_procedure(old.product_in_id, old.product_id);
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
        Schema::dropIfExists('product_in_details');
        DB::connection('mysql')->unprepared('DROP PROCEDURE IF EXISTS `product_in_details__product_monthly_movements_procedure`');
        DB::connection('mysql')->unprepared('DROP TRIGGER IF EXISTS product_in_details_after_insert_trigger');
        DB::connection('mysql')->unprepared('DROP TRIGGER IF EXISTS product_in_details_after_update_trigger');
        DB::connection('mysql')->unprepared('DROP TRIGGER IF EXISTS product_in_details_after_delete_trigger');
    }
}
