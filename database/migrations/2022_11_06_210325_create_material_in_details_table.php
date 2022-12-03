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
            $table->unique(['material_id', 'material_in_id']);
        });

        DB::connection('mysql')->unprepared('CREATE OR REPLACE PROCEDURE material_monthly_movements_upsert_in_procedure (
                IN materialID int,
                IN yearAt int,
                IN monthAt int
            )
            BEGIN
                INSERT INTO
                    material_monthly_movements (material_id, year, month, `in`)
                SELECT
                    material_id,
                    yearAt,
                    monthAt,
                    @total_qty := SUM(qty)
                FROM (SELECT materialID as material_id, mid.qty
                    FROM material_in_details AS mid
                    LEFT JOIN material_ins AS mi ON mid.material_in_id = mi.id
                    WHERE
                        mid.material_id = materialID AND
                        mi.deleted_at IS NULL AND
                        YEAR(mi.at) = yearAt AND
                        MONTH(mi.at) = monthAt
                UNION SELECT materialID, 0
                ) AS qty_temp
                GROUP BY material_id
                ON DUPLICATE KEY UPDATE `in` = @total_qty;
            END;
        ');

        DB::connection('mysql')->unprepared('CREATE OR REPLACE PROCEDURE material_in_details__material_monthly_movements_procedure (
                IN materialInID int,
                IN materialID int
            )
            BEGIN
                DECLARE yearAt int;
                DECLARE monthAt int;

                SELECT YEAR(`at`), MONTH(`at`) INTO yearAt, monthAt
                FROM material_ins
                WHERE id = materialInID;

                CALL material_monthly_movements_upsert_in_procedure(materialID, yearAt, monthAt);
            END;
        ');

        DB::connection('mysql')->unprepared('CREATE OR REPLACE TRIGGER material_ins_after_update_trigger
            AFTER UPDATE
            ON material_ins
            FOR EACH ROW
            BEGIN
                -- TODO: optimize this IF
                IF (OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL) OR (NEW.deleted_at IS NULL AND OLD.deleted_at IS NOT NULL) THEN
                    CALL material_in_details__material_monthly_movements_procedure(
                        OLD.id,
                        (
                            SELECT material_id
                            FROM material_in_details
                            WHERE material_in_id = OLD.id
                        )
                    );
                END IF;

                IF YEAR(NEW.at) <> YEAR(OLD.at) OR MONTH(NEW.at) <> MONTH(OLD.at) THEN
                    CALL material_monthly_movements_upsert_in_procedure(
                        (SELECT material_id FROM material_in_details WHERE material_in_id = OLD.id),
                        YEAR(OLD.at),
                        MONTH(OLD.at)
                    );

                    CALL material_in_details__material_monthly_movements_procedure(
                        OLD.id,
                        (SELECT material_id FROM material_in_details WHERE material_in_id = OLD.id)
                    );
                END IF;
            END;
        ');

        DB::connection('mysql')->unprepared('CREATE OR REPLACE TRIGGER material_in_details_after_insert_trigger
                AFTER INSERT
                ON material_in_details
                FOR EACH ROW
            BEGIN
                CALL material_in_details__material_monthly_movements_procedure(NEW.material_in_id, NEW.material_id);
            END;
        ');

        DB::connection('mysql')->unprepared('CREATE OR REPLACE TRIGGER material_in_details_after_update_trigger
                AFTER UPDATE
                ON material_in_details
                FOR EACH ROW
            BEGIN
                IF NEW.qty <> OLD.qty AND NEW.material_id = OLD.material_id THEN
                    CALL material_in_details__material_monthly_movements_procedure(NEW.material_in_id, NEW.material_id);
                END IF;

                IF NEW.material_id <> OLD.material_id THEN
                    CALL material_in_details__material_monthly_movements_procedure(NEW.material_in_id, OLD.material_id);
                    CALL material_in_details__material_monthly_movements_procedure(NEW.material_in_id, NEW.material_id);
                END IF;
            END;
        ');

        DB::connection('mysql')->unprepared('CREATE OR REPLACE TRIGGER material_in_details_after_delete_trigger
                AFTER DELETE
                ON material_in_details
                FOR EACH ROW
            BEGIN
                CALL material_in_details__material_monthly_movements_procedure(OLD.material_in_id, OLD.material_id);
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
        DB::connection('mysql')->unprepared('DROP PROCEDURE IF EXISTS `material_monthly_movements_upsert_in_procedure`');
        DB::connection('mysql')->unprepared('DROP PROCEDURE IF EXISTS `material_in_details__material_monthly_movements_procedure`');
        DB::connection('mysql')->unprepared('DROP TRIGGER IF EXISTS material_ins_after_update_trigger');
        DB::connection('mysql')->unprepared('DROP TRIGGER IF EXISTS material_in_details_after_insert_trigger');
        DB::connection('mysql')->unprepared('DROP TRIGGER IF EXISTS material_in_details_after_update_trigger');
        DB::connection('mysql')->unprepared('DROP TRIGGER IF EXISTS material_in_details_after_delete_trigger');
    }
}
