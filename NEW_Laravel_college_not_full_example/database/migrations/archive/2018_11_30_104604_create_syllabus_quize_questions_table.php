<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSyllabusQuizeQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('syllabus_quize_questions', function (Blueprint $table) {

            $table->unsignedInteger('syllabus_id');
            $table->foreign('syllabus_id')->references('id')->on('syllabus');

            $table->integer('quize_question_id');
            $table->foreign('quize_question_id')->references('id')->on('quize_questions');

            $table->timestamps();

            $table->primary(['syllabus_id', 'quize_question_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('syllabus_quize_questions');
    }
}
