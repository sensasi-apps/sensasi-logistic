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
        Schema::create('product_in_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_in_id')
                ->constrained('product_ins')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->float('qty', 16, 8);
            $table->float('price', 16, 8);
            $table->date('expired_at')->nullable();
            $table->date('manufactured_at')->nullable();
            $table->unique(['product_id', 'product_in_id']);
        });

        DB::unprepared('DROP PROCEDURE IF EXISTS `product_monthly_movements_upsert_in_procedure`');

        DB::unprepared('CREATE PROCEDURE product_monthly_movements_upsert_in_procedure (
                IN productID int,
                IN yearAt int,
                IN monthAt int
            )
            BEGIN
                INSERT INTO
                    product_monthly_movements (product_id, year, month, `in`, avg_in, avg_in_price)
                SELECT
                    product_id,
                    yearAt,
                    monthAt,
                    @total_qty := SUM(qty),
                    @avg_qty := AVG(qty),
                    @avg_in_price := AVG(CASE WHEN pid.price > 0 THEN pid.price ELSE NULL END)
                FROM product_ins pi
                LEFT JOIN product_in_details pid ON pi.id = pid.product_in_id
                WHERE
                    pid.product_id = productID AND
                    YEAR(`pi`.at) = yearAt AND
                    MONTH(`pi`.at) = monthAt AND
                    pid.qty > 0
                GROUP BY pid.product_id
                ON DUPLICATE KEY UPDATE `in` = @total_qty, avg_in = @avg_qty, avg_in_price = @avg_in_price;
            END;
        ');

        DB::unprepared('DROP PROCEDURE IF EXISTS `product_in_details__product_monthly_movements_procedure`');

        DB::unprepared('CREATE PROCEDURE product_in_details__product_monthly_movements_procedure (
                IN productInID int,
                IN productID int
            )
            BEGIN
                DECLARE yearAt int;
                DECLARE monthAt int;

                SELECT YEAR(`at`), MONTH(`at`) INTO yearAt, monthAt
                FROM product_ins
                WHERE id = productInID;

                CALL product_monthly_movements_upsert_in_procedure(productID, yearAt, monthAt);
            END;
        ');

        DB::unprepared('CREATE TRIGGER product_ins_after_update_trigger
                AFTER UPDATE
                ON product_ins
                FOR EACH ROW
            BEGIN
                DECLARE done INT DEFAULT FALSE;
                DECLARE product_id INT;

                DECLARE cur CURSOR FOR SELECT product_id FROM product_in_details WHERE product_in_id = OLD.id;
                DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

                SET @is_at_changed = YEAR(NEW.at) <> YEAR(OLD.at) OR MONTH(NEW.at) <> MONTH(OLD.at);

                OPEN cur;

                read_loop: LOOP
                    FETCH cur INTO product_id;
                    IF done THEN
                        LEAVE read_loop;
                    END IF;

                    IF @is_at_changed THEN
                        CALL product_monthly_movements_upsert_in_procedure(product_id, YEAR(OLD.at), MONTH(OLD.at));
                        CALL product_monthly_movements_upsert_in_procedure(product_id, YEAR(NEW.at), MONTH(NEW.at));
                    END IF;
                END LOOP;

                CLOSE cur;
            END;
        ');

        DB::unprepared('CREATE TRIGGER product_in_details_after_insert_trigger
                AFTER INSERT
                ON product_in_details
                FOR EACH ROW
            BEGIN
                CALL product_in_details__product_monthly_movements_procedure(NEW.product_in_id, NEW.product_id);
            END;
        ');

        DB::unprepared('CREATE TRIGGER product_in_details_after_update_trigger
                AFTER UPDATE
                ON product_in_details
                FOR EACH ROW
            BEGIN
                IF NEW.qty <> OLD.qty AND NEW.product_id = OLD.product_id THEN
                    CALL product_in_details__product_monthly_movements_procedure(NEW.product_in_id, NEW.product_id);
                END IF;

                IF NEW.product_id <> OLD.product_id THEN
                    CALL product_in_details__product_monthly_movements_procedure(NEW.product_in_id, OLD.product_id);
                    CALL product_in_details__product_monthly_movements_procedure(NEW.product_in_id, NEW.product_id);
                END IF;
            END;
            ');

        DB::unprepared('CREATE TRIGGER product_in_details_after_delete_trigger
                AFTER DELETE
                ON product_in_details
                FOR EACH ROW
            BEGIN
                CALL product_in_details__product_monthly_movements_procedure(OLD.product_in_id, OLD.product_id);
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
        DB::unprepared('DROP PROCEDURE IF EXISTS `product_monthly_movements_upsert_in_procedure`');
        DB::unprepared('DROP PROCEDURE IF EXISTS `product_in_details__product_monthly_movements_procedure`');
        DB::unprepared('DROP TRIGGER IF EXISTS product_ins_after_update_trigger');
        DB::unprepared('DROP TRIGGER IF EXISTS product_in_details_after_insert_trigger');
        DB::unprepared('DROP TRIGGER IF EXISTS product_in_details_after_update_trigger');
        DB::unprepared('DROP TRIGGER IF EXISTS product_in_details_after_delete_trigger');
    }
}
