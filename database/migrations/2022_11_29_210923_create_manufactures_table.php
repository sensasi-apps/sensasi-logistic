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
        Schema::connection('mysql')->create('manufactures', function (Blueprint $table) {
            $database = DB::connection('mysql_system')->getDatabaseName();
            $table->id();
            $table->string('code', 15)->nullable()->unique();
            $table->dateTime('at');
            $table->foreignId('created_by_user_id')
                ->constrained("$database.users")
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->text('note')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql')->dropIfExists('manufactures');
    }
}
