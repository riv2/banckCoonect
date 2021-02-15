<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDisciplineSemestersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discipline_semesters', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('discipline_id');
            $table->string('semester');
            $table->string('study_form');
            $table->string('lecture_hours')->nullable();
            $table->string('practical_hours')->nullable();
            $table->string('laboratory_hours')->nullable();
            $table->string('sro_hours')->nullable();
            $table->string('srop_hours')->nullable();
            $table->string('control_form')->nullable();
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
        Schema::dropIfExists('discipline_semesters');
    }
}
