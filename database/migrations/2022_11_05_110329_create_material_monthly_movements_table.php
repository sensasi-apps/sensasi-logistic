<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaterialMonthlyMovementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('material_monthly_movements', function (Blueprint $table) {
            $table->foreignId('material_id')
                ->constrained('materials')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month');
            $table->bigInteger('in')->default(0);
            $table->bigInteger('out')->default(0);
            $table->float('avg_in', 16, 8)->default(0);
            $table->float('avg_out', 16, 8)->default(0);
            $table->float('avg_in_price', 16, 8)->default(0);
            $table->float('avg_out_price', 16, 8)->default(0);
            
            $table->primary(['material_id', 'year', 'month']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('material_monthly_movements');
    }
}
