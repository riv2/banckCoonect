<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAppealQuizAnswersTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('appeal_quiz_answers')) {
            Schema::create(
                'appeal_quiz_answers',
                function (Blueprint $table) {
                    $table->increments('id');

                    $table->unsignedInteger('result_id');
                    $table->index('result_id');

                    $table->integer('question_id');
                    $table->text('question');

                    $table->integer('answer_id');
                    $table->text('answer');

                    $table->timestamps();
                }
            );
        }
    }

    public function down()
    {
        Schema::dropIfExists('appeal_quiz_answers');
    }
}