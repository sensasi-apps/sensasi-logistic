<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
            $table->unique('material_in_id', 'material_id');
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
        Schema::connection('mysql')->dropIfExists('material_in_details');
    }
}
