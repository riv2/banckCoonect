<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpecialityDisciplineTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('speciality_discipline', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('speciality_id');
            $table->foreign('speciality_id')->references('id')->on('specialities');

            $table->integer('discipline_id');
            $table->foreign('discipline_id')->references('id')->on('disciplines');

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
        Schema::dropIfExists('speciality_discipline');
    }
}
