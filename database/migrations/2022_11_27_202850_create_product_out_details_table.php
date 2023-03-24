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

            $table->float('qty', 16, 8);
            $table->float('price', 16, 8);
            $table->unique(['product_in_detail_id', 'product_out_id'], 'product_out_details_unique');
        });

        DB::unprepared('DROP PROCEDURE IF EXISTS `product_monthly_movements_upsert_out_procedure`');

        DB::unprepared('CREATE PROCEDURE product_monthly_movements_upsert_out_procedure (
            IN productID int,
            IN yearAt int,
            IN monthAt int
        )
            BEGIN
                INSERT INTO product_monthly_movements
                    (product_id, year, month, `out`, avg_out, avg_out_price)
                SELECT
                    pid.product_id,
                    yearAt,
                    monthAt,
                    @total_qty := SUM(`pod`.qty),
                    @avg_qty := AVG(`pod`.qty),
                    @avg_out_price := AVG(CASE WHEN `pod`.price > 0 THEN `pod`.price ELSE NULL END)
                FROM product_in_details AS pid
                JOIN product_out_details AS `pod` ON pid.id = `pod`.product_in_detail_id
                JOIN product_outs AS po ON `pod`.product_out_id = po.id
                WHERE
                    pid.product_id = productID AND
                    YEAR(po.at) = yearAt AND
                    MONTH(po.at) = monthAt AND
                    `pod`.qty > 0
                GROUP BY pid.product_id
                ON DUPLICATE KEY UPDATE `out` = @total_qty, avg_out = @avg_qty;
            END;
        ');

        DB::unprepared('DROP PROCEDURE IF EXISTS `product_out_details__product_monthly_movements_procedure`');

        DB::unprepared('CREATE PROCEDURE product_out_details__product_monthly_movements_procedure (
                IN productOutId int,
                IN productInDetailId int
            )
            BEGIN
                DECLARE yearAt int;
                DECLARE monthAt int;
                DECLARE productID int;

                SELECT YEAR(`po`.`at`), MONTH(`po`.`at`), `pid`.product_id INTO yearAt, monthAt, productID
                FROM product_out_details as `pod`
                LEFT JOIN product_outs AS po ON `pod`.product_out_id = po.id
                LEFT JOIN product_in_details AS pid ON `pod`.product_in_detail_id = pid.id
                WHERE `pod`.product_out_id = productOutId AND `pod`.product_in_detail_id = productInDetailId;

                CALL product_monthly_movements_upsert_out_procedure(
                    productID,
                    yearAt,
                    monthAt
                );
            END;
        ');

        DB::unprepared('CREATE TRIGGER product_outs_after_update_trigger
            AFTER UPDATE
            ON product_outs
            FOR EACH ROW
            BEGIN
                DECLARE done INT DEFAULT FALSE;
                DECLARE product_id INT;

                DECLARE cur CURSOR FOR SELECT
                    `pid`.product_id
                FROM product_out_details AS `pod`
                LEFT JOIN product_in_details AS pid ON `pod`.product_in_detail_id = pid.id
                WHERE product_out_id = OLD.id;

                DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

                SET @is_at_changed = YEAR(NEW.at) <> YEAR(OLD.at) OR MONTH(NEW.at) <> MONTH(OLD.at);

                OPEN cur;

                read_loop: LOOP
                    FETCH cur INTO product_id;
                    IF done THEN
                        LEAVE read_loop;
                    END IF;

                    IF @is_at_changed THEN
                        CALL product_monthly_movements_upsert_out_procedure(product_id, YEAR(OLD.at), MONTH(OLD.at));
                        CALL product_monthly_movements_upsert_out_procedure(product_id, YEAR(NEW.at), MONTH(NEW.at));
                    END IF;
                END LOOP;

                CLOSE cur;
            END;
        ');

        DB::unprepared('CREATE TRIGGER product_out_details_after_insert_trigger
                AFTER INSERT
                ON product_out_details
                FOR EACH ROW
            BEGIN
                CALL product_out_details__product_monthly_movements_procedure(NEW.product_out_id, NEW.product_in_detail_id);
            END;
        ');

        DB::unprepared('CREATE TRIGGER product_out_details_after_update_trigger
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

        DB::unprepared('CREATE TRIGGER product_out_details_after_delete_trigger
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
