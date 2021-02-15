<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpecialityEntranceTestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('speciality_entrance_test', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('speciality_id');
            $table->foreign('speciality_id')->references('id')->on('specialities');

            $table->unsignedInteger('entrance_test_id');
            $table->foreign('entrance_test_id')->references('id')->on('entrance_test');

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
        Schema::dropIfExists('speciality_entrance_test');
    }
}
