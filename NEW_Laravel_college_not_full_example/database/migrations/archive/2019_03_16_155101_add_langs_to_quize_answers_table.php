<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLangsToQuizeAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quize_answers', function (Blueprint $table) {
            $table->longText('answer_fr')->after('answer_kz');
            $table->longText('answer_ar')->after('answer_fr');
            $table->longText('answer_de')->after('answer_ar');
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
            $table->dropColumn('answer_fr');
            $table->dropColumn('answer_ar');
            $table->dropColumn('answer_de');
        });
    }
}
