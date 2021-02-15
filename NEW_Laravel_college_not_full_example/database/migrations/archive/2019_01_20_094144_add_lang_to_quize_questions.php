<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLangToQuizeQuestions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quize_questions', function (Blueprint $table) {
            $table->longText('question_en')->nullable()->after('question');
            $table->longText('question_kz')->nullable()->after('question_en');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quize_questions', function (Blueprint $table) {
            $table->dropColumn('question_en');
            $table->dropColumn('question_kz');
        });
    }
}
