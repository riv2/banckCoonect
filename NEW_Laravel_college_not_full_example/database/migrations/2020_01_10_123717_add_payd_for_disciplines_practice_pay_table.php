<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPaydForDisciplinesPracticePayTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('disciplines_practice_pay',
            function (Blueprint $table) {
                $table->integer('payed_sum')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('disciplines_practice_pay',
            function (Blueprint $table) {
                $table->dropColumn('payed_sum');
            }
        );
    }
}
