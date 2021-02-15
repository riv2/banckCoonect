<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddLangToQuizeResult extends Migration
{
    public function up()
    {
        Schema::table(
            'quize_result',
            function (Blueprint $table) {
                $table->char('lang', 2)->after('type')->nullable()->comment('Язык на котором сдавали');
            }
        );
    }

    public function down()
    {
        Schema::table(
            'quize_result',
            function (Blueprint $table) {
                $table->dropColumn('lang');
            }
        );
    }
}