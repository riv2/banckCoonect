<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class CreateBalanceHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('balance_history', function (Blueprint $table) {
            $table->id();
            $table->float('value');
            $table->float('balance');
            $table->integer('user_id');
            $table->dateTime('created_at');
        });

        Artisan::call('db:seed');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('balance_history');
    }
}
