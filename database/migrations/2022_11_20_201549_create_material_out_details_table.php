<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
            $table->foreignId('mat_in_detail_id')
            ->constrained('material_in_details')
            ->cascadeOnUpdate()
            ->restrictOnDelete();

            $table->foreignId('material_out_id')
            ->constrained('material_outs')
            ->cascadeOnUpdate()
            ->restrictOnDelete();

            $table->integer('qty');
            $table->unique(['mat_in_detail_id','material_out_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql')->dropIfExists('material_out_details');
    }
}
