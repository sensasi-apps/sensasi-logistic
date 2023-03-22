<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_manufactures', function (Blueprint $table) {
            $table->id();
            $table->string('code', 15)->nullable()->unique();
            $table->date('at');
            $table->text('note')->nullable();
            $table->foreignId('material_out_id')->unique()
                ->constrained('material_outs')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignId('product_in_id')->unique()
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
        Schema::dropIfExists('product_manufactures');
    }
};
