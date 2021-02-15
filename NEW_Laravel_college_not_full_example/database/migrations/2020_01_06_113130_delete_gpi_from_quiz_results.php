<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class DeleteGpiFromQuizResults extends Migration
{
    public function up()
    {
        Schema::table(
            'quize_result',
            function (Blueprint $table) {
                $table->dropColumn('gpi');
            }
        );


    }

    public function down()
    {
        Schema::table(
            'quize_result',
            function (Blueprint $table) {
                $table->float('gpi')->after('points')->nullable()->comment('Оценка gpi');
            }
        );
    }
}