<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateManufactureProductInTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql')->create('manufacture_product_in', function (Blueprint $table) {
            $table->foreignId('manufacture_id')
                ->constrained('manufactures')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignId('product_in_id')
                ->constrained('product_ins')
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
        Schema::connection('mysql')->dropIfExists('manufacture_product_in');
    }
}
