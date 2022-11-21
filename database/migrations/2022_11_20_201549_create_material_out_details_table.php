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
            $table->unique(['material_in_detail_id','material_out_id'], 'material_out_details_unique');
        });

        // DB::connection('mysql')->unprepared('
        //     CREATE PROCEDURE material_out_details__material_monthly_movements_procedure (
        //         IN materialOutId int,
        //         IN materialInDetailId int
        //     )
        //     BEGIN
        //         INSERT INTO
        //             material_monthly_movements (material_id, year, month, `out`)
        //         SELECT
        //             mid.material_id,
        //             YEAR(mo.at),
        //             MONTH(mo.at),
        //             @total_qty := SUM(mod.qty)
        //         FROM material_out_details mod
        //         LEFT JOIN material_in_details as mid ON mid.id = materialInDetailId
        //         LEFT JOIN material_outs as mo ON mo.id = materialOutId
        //         LEFT JOIN (
        //             SELECT mod.qty
        //             FROM material_out_detail
        //             LEFT JOIN material_oute
        //         )
        //         WHERE
        //             mid.material_id = materialID
        //         GROUP BY mid.material_id, YEAR(mi.at), MONTH(mi.at)
        //         ON DUPLICATE KEY UPDATE `out` = @total_qty;
        //     END;
        // ');

        // DB::connection('mysql')->unprepared('
        //     CREATE TRIGGER material_out_details_before_insert_trigger
        //         AFTER INSERT
        //         ON material_in_details
        //         FOR EACH ROW
        //     BEGIN
        //         SELECT @qty_in := `in`
        //         FROM material_in_detail
        //         WHERE id = new.material_in_detail_id;

        //         SELECT @qty_out := SUM(qty)
        //         FROM material_out_details
        //         WHERE material_in_detail_id = new.material_in_detail_id;

        //         IF NEW.qty > @qty_in - @qty_out
        //             signal sqlstate \'45000\' set message_text = \'Material is not enough\';
        //         END IF;
        //     END;
        // ');

        // DB::connection('mysql')->unprepared('
        //     CREATE TRIGGER material_out_details_after_insert_trigger
        //         AFTER INSERT
        //         ON material_in_details
        //         FOR EACH ROW
        //     BEGIN
        //         CALL material_out_details__material_monthly_movements_procedure(NEW.material_in_id, NEW.material_id);
        //     END;
        // ');

        // DB::connection('mysql')->unprepared('
        //     CREATE TRIGGER material_out_details_before_update_trigger
        //         BEFORE UPDATE
        //         ON material_in_details
        //         FOR EACH ROW
        //     BEGIN
        //         DECLARE n_rest int;
                
        //         IF NEW.qty < OLD.qty THEN
        //             SELECT `in`-`out` INTO n_rest
        //             FROM
        //                 material_monthly_movements mmm
        //             LEFT JOIN material_ins as mi ON mi.id = new.material_in_id
        //             WHERE
        //                 mmm.material_id = new.material_id AND
        //                 mmm.year = YEAR(mi.at) AND
        //                 mmm.month = MONTH(mi.at);

        //             IF OLD.qty - NEW.qty > n_rest THEN                    
        //                 SET NEW.qty = OLD.qty;
        //             END IF;
        //         END IF;
        //     END;
        // ');

        // DB::connection('mysql')->unprepared('
        //     CREATE TRIGGER material_out_details_after_update_trigger
        //         AFTER UPDATE
        //         ON material_in_details
        //         FOR EACH ROW
        //     BEGIN
        //         IF NEW.qty <> OLD.qty THEN               
        //             CALL material_out_details__material_monthly_movements_procedure(NEW.material_in_id, NEW.material_id);
        //         END IF;
        //     END;
        // ');

        // DB::connection('mysql')->unprepared('
        //     CREATE TRIGGER material_out_details_after_delete_trigger
        //         AFTER DELETE
        //         ON material_in_details
        //         FOR EACH ROW
        //     BEGIN
        //         CALL material_out_details__material_monthly_movements_procedure(old.material_in_id, old.material_id);
        //     END;
        // ');

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
        DB::connection('mysql')->unprepared('DROP TRIGGER IF EXISTS material_out_details_before_insert_trigger');
        DB::connection('mysql')->unprepared('DROP TRIGGER IF EXISTS material_out_details_after_insert_trigger');
        DB::connection('mysql')->unprepared('DROP TRIGGER IF EXISTS material_out_details_before_update_trigger');
        DB::connection('mysql')->unprepared('DROP TRIGGER IF EXISTS material_out_details_after_update_trigger');
        DB::connection('mysql')->unprepared('DROP TRIGGER IF EXISTS material_out_details_after_delete_trigger');
    }
}
