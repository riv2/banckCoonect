<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ChangeIndexOnQuizeQuestionsTable extends Migration
{
    public function up()
    {
        Schema::table(
            'quize_questions',
            function (Blueprint $table) {
                $table->dropIndex('quize_questions_discipline_id_index');
                 $table->foreign('discipline_id')->references('id')->on('disciplines')->onDelete('cascade');
            }
        );
    }

    public function down()
    {
        Schema::table(
            'quize_questions',
            function (Blueprint $table) {
                $table->dropForeign('quize_questions_discipline_id_foreign');
                $table->index('discipline_id');
            }
        );
    }
}