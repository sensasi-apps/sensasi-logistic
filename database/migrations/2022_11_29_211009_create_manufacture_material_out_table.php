<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateManufactureMaterialOutTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql')->create('manufacture_material_out', function (Blueprint $table) {
            $table->foreignId('material_out_id')
                ->constrained('material_outs')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignId('manufacture_id')
                ->constrained('manufactures')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql')->dropIfExists('manufacture_material_out');
    }
}
