<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ChangeIndexOnQuizeAnswersTable extends Migration
{
    public function up()
    {
        Schema::table(
            'quize_answers',
            function (Blueprint $table) {
                $table->dropIndex('quize_answers_question_id_index');
                $table->foreign('question_id')->references('id')->on('quize_questions')->onDelete('cascade');
            }
        );
    }

    public function down()
    {
        Schema::table(
            'quize_answers',
            function (Blueprint $table) {
                $table->dropForeign('quize_answers_question_id_foreign');
                $table->index('question_id', 'quize_answers_question_id_index');
            }
        );
    }
}