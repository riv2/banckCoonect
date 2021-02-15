<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class DeleteLangsFromQuizeQuestions extends Migration
{
    public function up()
    {
        Schema::table(
            'quize_questions',
            function (Blueprint $table) {
                $table->dropColumn('question_en');
                $table->dropColumn('question_kz');
                $table->dropColumn('question_fr');
                $table->dropColumn('question_ar');
                $table->dropColumn('question_de');
            }
        );
    }

    public function down()
    {
        Schema::table(
            'quize_questions',
            function (Blueprint $table) {
                $table->longText('question_en')->after('question')->nullable();
                $table->longText('question_kz')->after('question_en')->nullable();
                $table->longText('question_fr')->after('question_kz')->nullable();
                $table->longText('question_ar')->after('question_fr')->nullable();
                $table->longText('question_de')->after('question_ar')->nullable();
            }
        );
    }
}