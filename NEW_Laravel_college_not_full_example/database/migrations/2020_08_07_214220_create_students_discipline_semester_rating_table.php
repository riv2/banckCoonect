<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentsDisciplineSemesterRatingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students_discipline_semester_rating', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->on('profiles')->references('user_id');
            $table->integer('discipline_id');
            $table->foreign('discipline_id')->on('disciplines')->references('id');
            $table->integer('teacher_id')->unsigned();
            $table->foreign('teacher_id')->on('users')->references('id');
            $table->integer('study_group_id')->unsigned();
            $table->foreign('study_group_id')->on('study_groups')->references('id');
            $table->string('semester');
            $table->string('rating')->nullable();
            $table->string('type')->default('default_rating');
            $table->integer('month')->nullable();
            $table->integer('year')->nullable();
            $table->integer('day')->nullable();
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
        Schema::dropIfExists('students_discipline_semester_rating');
    }
}
