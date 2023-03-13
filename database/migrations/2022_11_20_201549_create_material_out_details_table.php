<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateMaterialOutDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('material_out_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_in_detail_id')
                ->constrained('material_in_details')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('material_out_id')
                ->constrained('material_outs')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->integer('qty');
            $table->unique(['material_in_detail_id', 'material_out_id'], 'material_out_details_unique');
        });

        DB::unprepared('DROP PROCEDURE IF EXISTS `material_monthly_movements_upsert_out_procedure`');

        DB::unprepared('CREATE PROCEDURE
            material_monthly_movements_upsert_out_procedure (
                IN materialID int,
                IN yearAt int,
                IN monthAt int
            )
            BEGIN
                INSERT INTO material_monthly_movements
                    (material_id, year, month, `out`, avg_out, avg_out_price)
                SELECT
                    mid.material_id,
                    yearAt,
                    monthAt,
                    @total_qty := SUM(`mod`.qty),
                    @avg_out := AVG(`mod`.qty),
                    @avg_out_price := AVG(CASE WHEN `mid`.price > 0 THEN `mid`.price ELSE NULL END)
                FROM material_in_details AS mid
                JOIN material_out_details AS `mod` ON mid.id = `mod`.material_in_detail_id
                JOIN material_outs AS mo ON `mod`.material_out_id = mo.id
                WHERE
                    mid.material_id = materialID AND
                    YEAR(mo.at) = yearAt AND
                    MONTH(mo.at) = monthAt AND
                    `mod`.qty > 0
                GROUP BY mid.material_id
                ON DUPLICATE KEY UPDATE `out` = @total_qty, avg_out = @avg_out, avg_out_price = @avg_out_price;
            END;
        ');

        DB::unprepared('DROP PROCEDURE IF EXISTS `material_out_details__material_monthly_movements_procedure`');

        DB::unprepared('CREATE PROCEDURE material_out_details__material_monthly_movements_procedure (
                IN materialOutId int,
                IN materialInDetailId int
            )
            BEGIN
                DECLARE yearAt int;
                DECLARE monthAt int;
                DECLARE materialID int;

                SELECT YEAR(`mo`.`at`), MONTH(`mo`.`at`), `mid`.material_id INTO yearAt, monthAt, materialID
                FROM material_out_details as `mod`
                LEFT JOIN material_outs AS mo ON `mod`.material_out_id = mo.id
                LEFT JOIN material_in_details AS mid ON `mod`.material_in_detail_id = mid.id
                WHERE `mod`.material_out_id = materialOutId AND `mod`.material_in_detail_id = materialInDetailId;

                CALL material_monthly_movements_upsert_out_procedure(
                    materialID,
                    yearAt,
                    monthAt
                );
            END;
        ');

        DB::unprepared('CREATE TRIGGER material_outs_after_update_trigger
            AFTER UPDATE
            ON material_outs
            FOR EACH ROW
            BEGIN
                DECLARE done INT DEFAULT FALSE;
                DECLARE material_id INT;

                DECLARE cur CURSOR FOR SELECT
                    `mid`.material_id
                FROM material_out_details AS `mod`
                LEFT JOIN material_in_details AS mid ON `mod`.material_in_detail_id = mid.id
                WHERE material_out_id = OLD.id;

                DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

                SET @is_at_changed = YEAR(NEW.at) <> YEAR(OLD.at) OR MONTH(NEW.at) <> MONTH(OLD.at);

                OPEN cur;

                read_loop: LOOP
                    FETCH cur INTO material_id;
                    IF done THEN
                        LEAVE read_loop;
                    END IF;

                    IF @is_at_changed THEN
                        CALL material_monthly_movements_upsert_out_procedure(material_id, YEAR(OLD.at), MONTH(OLD.at));
                        CALL material_monthly_movements_upsert_out_procedure(material_id, YEAR(NEW.at), MONTH(NEW.at));
                    END IF;
                END LOOP;

                CLOSE cur;
            END;
        ');

        DB::unprepared('CREATE TRIGGER material_out_details_after_insert_trigger
                AFTER INSERT
                ON material_out_details
                FOR EACH ROW
            BEGIN
                CALL material_out_details__material_monthly_movements_procedure(NEW.material_out_id, NEW.material_in_detail_id);
            END;
        ');

        DB::unprepared('CREATE TRIGGER material_out_details_after_update_trigger
                AFTER UPDATE
                ON material_out_details
                FOR EACH ROW
            BEGIN
                IF NEW.qty <> OLD.qty AND NEW.material_in_detail_id = OLD.material_in_detail_id THEN
                    CALL material_out_details__material_monthly_movements_procedure(NEW.material_out_id, NEW.material_in_detail_id);
                END IF;

                IF NEW.material_in_detail_id <> OLD.material_in_detail_id THEN
                    CALL material_out_details__material_monthly_movements_procedure(NEW.material_out_id, OLD.material_in_detail_id);
                    CALL material_out_details__material_monthly_movements_procedure(NEW.material_out_id, NEW.material_in_detail_id);
                END IF;
            END;
        ');

        DB::unprepared('CREATE TRIGGER material_out_details_after_delete_trigger
                AFTER DELETE
                ON material_out_details
                FOR EACH ROW
            BEGIN
                CALL material_out_details__material_monthly_movements_procedure(OLD.material_out_id, OLD.material_in_detail_id);

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
        Schema::dropIfExists('material_out_details');
        DB::unprepared('DROP PROCEDURE IF EXISTS `material_monthly_movements_upsert_out_procedure`');
        DB::unprepared('DROP PROCEDURE IF EXISTS `material_out_details__material_monthly_movements_procedure`');
        DB::unprepared('DROP TRIGGER IF EXISTS material_outs_after_update_trigger');
        DB::unprepared('DROP TRIGGER IF EXISTS material_out_details_after_insert_trigger');
        DB::unprepared('DROP TRIGGER IF EXISTS material_out_details_after_update_trigger');
        DB::unprepared('DROP TRIGGER IF EXISTS material_out_details_after_delete_trigger');
    }
}
