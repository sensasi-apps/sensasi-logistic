<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->nullable()->unique();
            $table->string('brand')->nullable();
            $table->string('name');
            $table->string('tags_json')->nullable();
            $table->float('default_price', 16, 8);
            $table->integer('low_qty')->nullable();
            $table->string('unit', 10);
            $table->timestamps();

            $table->unique(['brand', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
