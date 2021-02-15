<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ChangeAnswerIdOnAppealQuizAnswers extends Migration
{
    public function up()
    {
        Schema::table(
            'appeal_quiz_answers',
            function (Blueprint $table) {
                $table->integer('answer_id')->nullable()->change();
                $table->text('answer')->nullable()->change();
            }
        );
    }

    public function down()
    {
        Schema::table(
            'appeal_quiz_answers',
            function (Blueprint $table) {
                $table->integer('answer_id')->nullable()->change();
                $table->text('answer')->nullable()->change();
            }
        );
    }
}