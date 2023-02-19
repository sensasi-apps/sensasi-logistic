<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateManufacturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manufactures', function (Blueprint $table) {
            $table->id();
            $table->string('code', 15)->nullable()->unique();
            $table->dateTime('at');
            $table->text('note')->nullable();
            $table->foreignId('material_out_id')
                ->constrained('material_outs')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignId('product_in_id')
                ->constrained('product_ins')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
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
        Schema::dropIfExists('manufactures');
    }
}
