<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLangsToQuizeQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quize_questions', function (Blueprint $table) {
            $table->longText('question_fr')->after('question_kz');
            $table->longText('question_ar')->after('question_fr');
            $table->longText('question_de')->after('question_ar');
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
            $table->dropColumn('question_fr');
            $table->dropColumn('question_ar');
            $table->dropColumn('question_de');
        });
    }
}
