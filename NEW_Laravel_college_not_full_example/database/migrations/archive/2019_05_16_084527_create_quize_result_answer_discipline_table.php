<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuizeResultAnswerDisciplineTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quize_result_answer_discipline', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('question_id');
            $table->foreign('question_id')->references('id')->on('quize_questions')->onDelete('cascade');

            $table->integer('answer_id');
            $table->foreign('answer_id')->references('id')->on('quize_answers')->onDelete('cascade');

            $table->unsignedInteger('result_id');
            $table->foreign('result_id')->references('id')->on('quize_result')->onDelete('cascade');

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
        Schema::dropIfExists('quize_result_answer_discipline');
    }
}
