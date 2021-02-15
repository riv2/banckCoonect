<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPointsAndGpiToQuizeResultTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quize_result', function (Blueprint $table) {
            $table->float('points')->after('value')->nullable()->comment('Оценка в баллах');
            $table->float('gpi')->after('points')->nullable()->comment('Оценка gpi');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quize_result', function (Blueprint $table) {
            $table->dropColumn('points');
            $table->dropColumn('gpi');
        });
    }
}
