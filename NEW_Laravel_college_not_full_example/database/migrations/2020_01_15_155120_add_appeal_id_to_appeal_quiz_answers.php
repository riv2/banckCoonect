<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddAppealIdToAppealQuizAnswers extends Migration
{
    public function up()
    {
        Schema::table(
            'appeal_quiz_answers',
            function (Blueprint $table) {
                $table->unsignedInteger('appeal_id')->after('id');
                $table->foreign('appeal_id')->references('id')->on('appeals')->onDelete('cascade');
            }
        );
    }

    public function down()
    {
        Schema::table(
            'appeal_quiz_answers',
            function (Blueprint $table) {
                $table->dropColumn('appeal_id');
            }
        );
    }
}