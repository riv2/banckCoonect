<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class DeleteLangsFromQuizeAnswers extends Migration
{
    public function up()
    {
//        Schema::table(
//            'quize_answers',
//            function (Blueprint $table) {
//                $table->dropColumn('answer_en');
//                $table->dropColumn('answer_kz');
//                $table->dropColumn('answer_fr');
//                $table->dropColumn('answer_ar');
//                $table->dropColumn('answer_de');
//            }
//        );
    }

    public function down()
    {
        Schema::table(
            'quize_answers',
            function (Blueprint $table) {
                $table->longText('answer_en')->after('answer')->nullable();
                $table->longText('answer_kz')->after('answer_en')->nullable();
                $table->longText('answer_fr')->after('answer_kz')->nullable();
                $table->longText('answer_ar')->after('answer_fr')->nullable();
                $table->longText('answer_de')->after('answer_ar')->nullable();
            }
        );
    }
}