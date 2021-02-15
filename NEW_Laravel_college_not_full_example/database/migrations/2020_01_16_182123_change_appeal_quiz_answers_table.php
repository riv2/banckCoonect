<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ChangeAppealQuizAnswersTable extends Migration
{
    public function up()
    {
        Schema::table(
            'appeal_quiz_answers',
            function (Blueprint $table) {
                $table->dropColumn('question_id');
                $table->dropColumn('question');
                $table->dropColumn('answer_id');
                $table->dropColumn('answer');

                $table->longText('snapshot')->after('result_id');
            }
        );
    }

    public function down()
    {
        Schema::table(
            'appeal_quiz_answers',
            function (Blueprint $table) {
                $table->dropColumn('snapshot');

                $table->integer('question_id');
                $table->text('question');
                $table->integer('answer_id');
                $table->text('answer');
            }
        );
    }
}