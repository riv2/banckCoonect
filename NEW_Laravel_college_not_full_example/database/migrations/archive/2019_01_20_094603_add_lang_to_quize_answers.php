<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLangToQuizeAnswers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quize_answers', function (Blueprint $table) {
            $table->longText('answer_en')->nullable()->after('answer');
            $table->longText('answer_kz')->nullable()->after('answer_en');
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
            $table->dropColumn('answer_en');
            $table->dropColumn('answer_kz');
        });
    }
}
