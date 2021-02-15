<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FinanceNomenclaturesAddNewColumnCost extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('finance_nomenclatures', function (Blueprint $table) {
            $table->string('name_kz',255)->nullable(true);
            $table->string('name_en',255)->nullable(true);
            $table->enum('type',['fee','other'])->default('fee');
            $table->integer('cost');
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
            $table->dropColumn('name_kz');
            $table->dropColumn('name_en');
            $table->dropColumn('type');
            $table->dropColumn('cost');
        });
    }
}
