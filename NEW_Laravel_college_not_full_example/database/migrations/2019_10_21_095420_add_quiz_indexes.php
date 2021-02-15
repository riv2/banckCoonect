<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddQuizIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quize_answers', function (Blueprint $table) {
//            $table->foreign('question_id')->references('id')->on('quize_questions')->onDelete('cascade');
            $table->index('question_id');
        });

        Schema::table('quize_questions', function (Blueprint $table) {
//            $table->foreign('discipline_id')->references('id')->on('disciplines')->onDelete('cascade');
            $table->index('discipline_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quize_answers', function (Blueprint $table) {
            $table->dropIndex('quize_answers_question_id_index');
        });
        Schema::table('quize_questions', function (Blueprint $table) {
            $table->dropIndex('quize_questions_discipline_id_index');
        });
    }
}
