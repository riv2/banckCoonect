<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableWifiDevices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('wifi_devices')) {
            Schema::create('wifi_devices', function (Blueprint $table) {
                $table->increments('id');

                $table->string('mac', 64)->nullable();
                $table->string('name', 64)->nullable();
                $table->integer('user_id')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wifi_devices');
    }
}
