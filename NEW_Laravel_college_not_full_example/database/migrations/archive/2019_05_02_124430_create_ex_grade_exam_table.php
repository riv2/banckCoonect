<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExGradeExamTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ex_grade_exam', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('student_id');
            $table->unsignedInteger('group_id');
            $table->unsignedInteger('discipline_id')->nullable();
            $table->dateTime('date')->nullable();
            $table->integer('value')->nullable();
            $table->enum('type', ['AUTO', 'TEST', 'EXAM']);
            $table->integer('teacher_id')->nullable();
            $table->integer('plan_item_discipline_id');

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
        Schema::dropIfExists('ex_grade_exam');
    }
}
