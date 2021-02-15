<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMinsAndMbsUpDownToWifiTariff extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wifi_tariff', function (Blueprint $table) {
            $table->integer('mins')->nullable()->comment('order expire in minutes');
            $table->integer('mbs')->nullable()->comment('order can have maximum megabytes');
            $table->integer('up_speed')->nullable()->comment('maximum upload speed');
            $table->integer('down_speed')->nullable()->comment('minimum upload speed');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wifi_tariff', function (Blueprint $table) {
            $table->dropColumn('mins');
            $table->dropColumn('mbs');
            $table->dropColumn('up_speed');
            $table->dropColumn('down_speed');
        });
    }
}
