<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SyllabusTaskResultAnswerCreateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('syllabus_task_result_answer')) {
            Schema::create('syllabus_task_result_answer', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('question_id');
                $table->unsignedInteger('answer_id');
                $table->unsignedInteger('result_id');

                $table->timestamps();
                $table->softDeletes();

                $table->foreign('question_id')->references('id')->on('syllabus_task_questions');
                $table->foreign('answer_id')->references('id')->on('syllabus_task_answer');
                $table->foreign('result_id')->references('id')->on('syllabus_task_result');

            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('syllabus_task_result_answer');
    }
}
