<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateUserActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->string('action');
            $table->string('model')->nullable();
            $table->bigInteger('model_id')->nullable();
            $table->json('value')->nullable();
            $table->timestamp('at')->default(DB::raw('CURRENT_TIMESTAMP(0)'));
            $table->string('ip')->nullable();
            $table->string('browser')->nullable();
            $table->string('os')->nullable();
            $table->string('device')->nullable();
            $table->string('location')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_activities');
    }
}
