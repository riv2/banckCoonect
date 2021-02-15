<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTarifIdToWifi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wifi', function (Blueprint $table) {
            $table->integer('tariff_id')->nullable();
            $table->dropColumn('code');
            $table->dropColumn('value');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wifi', function (Blueprint $table) {
            $table->dropColumn('tariff_id');
            $table->float('cost');
            $table->integer('value');
        });
    }
}
