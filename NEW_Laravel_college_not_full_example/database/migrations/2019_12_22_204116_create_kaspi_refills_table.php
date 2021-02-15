<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKaspiRefillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('kaspi_refills')) {
            Schema::create('kaspi_refills', function (Blueprint $table) {
                $table->increments('id');
                $table->timestamps();
                $table->string('kaspi_transaction');
                $table->string('iin')->nullable();
                $table->string('fio')->nullable();
                $table->integer('amount')->nullable()->comment('in tiyn');
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
        Schema::dropIfExists('kaspi_refills');
    }
}
