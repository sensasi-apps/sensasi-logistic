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
        Schema::connection('mysql')->create('material_out_details', function (Blueprint $table) {
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

        DB::connection('mysql')->unprepared('CREATE PROCEDURE
            material_out_details__material_monthly_movements_procedure (
                IN materialOutId int,
                IN materialInDetailId int
            )
            BEGIN
                DECLARE year_at int;
                DECLARE month_at int;

                SELECT YEAR(`at`), MONTH(`at`) INTO year_at, month_at
                FROM material_outs
                WHERE id = materialOutId;

                INSERT INTO
                    material_monthly_movements (material_id, year, month, `out`)
                SELECT
                    mid.material_id,
                    YEAR(mo.at),
                    MONTH(mo.at),
                    @total_qty := SUM(`mod`.qty)
                FROM material_out_details `mod`
                LEFT JOIN material_in_details as mid ON mid.id = `mod`.material_in_detail_id
                LEFT JOIN material_outs as mo ON mo.id = `mod`.material_out_id
                WHERE
                    `mod`.material_in_detail_id = materialInDetailId AND
                    YEAR(mo.at) = year_at AND
                    MONTH(mo.at) = month_at 
                GROUP BY mid.material_id, YEAR(mo.at), MONTH(mo.at)
                ON DUPLICATE KEY UPDATE `out` = @total_qty;
            END;
        ');


        // DB::connection('mysql')->unprepared('CREATE PROCEDURE
        //     mod_stock_check_procedure (
        //         IN materialInDetailId int,
        //         IN newQty int
        //     )
        //     BEGIN
        //         SELECT
        //             @qty_in := mid.qty,
        //             @qty_out := COALESCE(SUM(`mod`.qty), 0)
        //         FROM material_out_details `mod`
        //         LEFT JOIN material_in_details mid ON mid.id = `mod`.material_in_detail_id
        //         WHERE `mod`.material_in_detail_id = materialInDetailId
        //         GROUP BY mid.id;

        //         IF newQty > (@qty_in - @qty_out)
        //             SIGNAL SQLSTATE \'45000\'
        //                 SET MESSAGE_TEXT = \'Material is not enough\';
        //         END IF;
        //     END;
        // ');

        // DB::connection('mysql')->unprepared('CREATE TRIGGER
        //     material_out_details_before_insert_trigger
        //         AFTER INSERT
        //         ON material_in_details
        //         FOR EACH ROW
        //     BEGIN
        //         CALL mod_stock_check_procedure(NEW.material_in_detail_id, NEW.qty)
        //     END;
        // ');

        DB::connection('mysql')->unprepared('CREATE TRIGGER
            material_out_details_after_insert_trigger
                AFTER INSERT
                ON material_out_details
                FOR EACH ROW
            BEGIN
                CALL material_out_details__material_monthly_movements_procedure(NEW.material_out_id, NEW.material_in_detail_id);
            END;
        ');

        // DB::connection('mysql')->unprepared('CREATE TRIGGER
        //     material_out_details_before_update_trigger
        //         BEFORE UPDATE
        //         ON material_out_details
        //         FOR EACH ROW
        //     BEGIN
        //         CALL mod_stock_check_procedure(NEW.material_in_detail_id, NEW.qty - OLD.qty)
        //     END;
        // ');

        DB::connection('mysql')->unprepared('CREATE TRIGGER
            material_out_details_after_update_trigger
                AFTER UPDATE
                ON material_out_details
                FOR EACH ROW
            BEGIN
                IF NEW.qty <> OLD.qty THEN               
                    CALL material_out_details__material_monthly_movements_procedure(NEW.material_out_id, NEW.material_in_detail_id);
                END IF;
            END;
        ');

        DB::connection('mysql')->unprepared('CREATE
            TRIGGER material_out_details_after_delete_trigger
                AFTER DELETE
                ON material_out_details
                FOR EACH ROW
            BEGIN
                CALL material_out_details__material_monthly_movements_procedure(old.material_out_id, old.material_in_detail_id);
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
        Schema::connection('mysql')->dropIfExists('material_out_details');
        DB::connection('mysql')->unprepared('DROP PROCEDURE IF EXISTS `material_out_details__material_monthly_movements_procedure`');
        // DB::connection('mysql')->unprepared('DROP PROCEDURE IF EXISTS `mod_stock_check_procedure`');
        // DB::connection('mysql')->unprepared('DROP TRIGGER IF EXISTS material_out_details_before_insert_trigger');
        DB::connection('mysql')->unprepared('DROP TRIGGER IF EXISTS material_out_details_after_insert_trigger');
        // DB::connection('mysql')->unprepared('DROP TRIGGER IF EXISTS material_out_details_before_update_trigger');
        DB::connection('mysql')->unprepared('DROP TRIGGER IF EXISTS material_out_details_after_update_trigger');
        DB::connection('mysql')->unprepared('DROP TRIGGER IF EXISTS material_out_details_after_delete_trigger');
    }
}
