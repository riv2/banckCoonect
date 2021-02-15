<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpecialitiesSubjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('speciality_subject', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('speciality_id');
            $table->foreign('speciality_id')->references('id')->on('specialities');

            $table->unsignedInteger('subject_id');
            $table->foreign('subject_id')->references('id')->on('subjects');

            $table->float('ent');

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
        Schema::dropIfExists('speciality_subject');
    }
}
