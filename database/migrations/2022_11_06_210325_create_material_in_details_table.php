<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateMaterialInDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql')->create('material_in_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_in_id')
            ->constrained('material_ins')
            ->cascadeOnUpdate()
            ->restrictOnDelete();

            $table->foreignId('material_id')
            ->constrained('materials')
            ->cascadeOnUpdate()
            ->restrictOnDelete();

            $table->integer('qty');
            $table->integer('price');
            $table->unique(['material_id','material_in_id']);
        });

  
        DB::connection('mysql')->unprepared('
            CREATE PROCEDURE material_in_details__material_monthly_movements_procedure (
                IN materialInID int,
                IN materialID int
            )
            BEGIN
                INSERT INTO
                    material_monthly_movements (material_id, year, month, `in`)
                SELECT
                    mid.material_id,
                    YEAR(mi.at),
                    MONTH(mi.at),
                    @total_qty := SUM(mid.qty)
                FROM material_in_details mid
                LEFT JOIN material_ins as mi ON mi.id = materialInID
                WHERE
                    mid.material_id = materialID
                GROUP BY mid.material_id, YEAR(mi.at), MONTH(mi.at)
                ON DUPLICATE KEY UPDATE `in` = @total_qty;
            END;
        ');

        DB::connection('mysql')->unprepared('
            CREATE TRIGGER material_in_details_after_insert_trigger
                AFTER INSERT
                ON material_in_details
                FOR EACH ROW
            BEGIN
                CALL material_in_details__material_monthly_movements_procedure(NEW.material_in_id, NEW.material_id);
            END;
        ');

        DB::connection('mysql')->unprepared('
            CREATE TRIGGER material_in_details_before_update_trigger
                BEFORE UPDATE
                ON material_in_details
                FOR EACH ROW
            BEGIN
                DECLARE n_rest int;

                IF NEW.qty < OLD.qty THEN
                    SELECT `in`-`out` INTO n_rest
                    FROM
                        material_monthly_movements mmm
                    LEFT JOIN material_ins as mi ON mi.id = new.material_in_id
                    WHERE
                        mmm.material_id = new.material_id AND
                        mmm.year = YEAR(mi.at) AND
                        mmm.month = MONTH(mi.at);

                    IF OLD.qty - NEW.qty > n_rest THEN                    
                        SET NEW.qty = OLD.qty;
                    END IF;
                END IF;
            END;
        ');

        DB::connection('mysql')->unprepared('
            CREATE TRIGGER material_in_details_after_update_trigger
                AFTER UPDATE
                ON material_in_details
                FOR EACH ROW
            BEGIN
                IF NEW.qty <> OLD.qty THEN               
                    CALL material_in_details__material_monthly_movements_procedure(NEW.material_in_id, NEW.material_id);
                END IF;
            END;
        ');

        DB::connection('mysql')->unprepared('
            CREATE TRIGGER material_in_details_after_delete_trigger
                AFTER DELETE
                ON material_in_details
                FOR EACH ROW
            BEGIN
                CALL material_in_details__material_monthly_movements_procedure(old.material_in_id, old.material_id);
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
        Schema::connection('mysql')->dropIfExists('material_in_details');
        DB::connection('mysql')->unprepared('DROP PROCEDURE IF EXISTS `material_in_details__material_monthly_movements_procedure`');
        DB::connection('mysql')->unprepared('DROP TRIGGER IF EXISTS material_in_details_after_insert_trigger');
        DB::connection('mysql')->unprepared('DROP TRIGGER IF EXISTS material_in_details_before_update_trigger');
        DB::connection('mysql')->unprepared('DROP TRIGGER IF EXISTS material_in_details_after_update_trigger');
        DB::connection('mysql')->unprepared('DROP TRIGGER IF EXISTS material_in_details_after_delete_trigger');
    }
}
