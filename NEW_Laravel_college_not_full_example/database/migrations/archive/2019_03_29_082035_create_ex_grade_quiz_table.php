<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExGradeQuizTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ex_grade_quiz', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('value')->nullable();
            $table->dateTime('date')->nullable();
            $table->integer('quiz_number')->nullable();
            $table->unsignedInteger('discipline_id')->nullable();
            $table->unsignedInteger('student_id');
            $table->unsignedInteger('group_id');
            $table->integer('attempt_count')->nullable();
            $table->unsignedInteger('plan_item_discipline_id');

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
        Schema::dropIfExists('ex_grade_quiz');
    }
}
