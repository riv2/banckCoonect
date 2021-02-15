<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOnlyOneToFinanceNomenclatures extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('finance_nomenclatures', function (Blueprint $table) {
            $table->boolean('only_one')->default(0)->comment('Разрешена только одна покупка для студента');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('finance_nomenclatures', function (Blueprint $table) {
            $table->dropColumn('only_one');
        });
    }
}
