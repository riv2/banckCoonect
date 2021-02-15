<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FinanceNomenclaturesAddNewColumnOrHidden extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('finance_nomenclatures', function (Blueprint $table) {
            $table->tinyInteger('or_hidden')->default(0);
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
            $table->dropColumn('or_hidden');
        });
    }
}
