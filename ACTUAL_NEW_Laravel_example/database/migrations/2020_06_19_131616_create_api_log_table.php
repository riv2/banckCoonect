<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApiLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_log', function (Blueprint $table) {
            $table->id('api_id');
            $table->integer('offer_id')->nullable();
            $table->string('order_id', 32)->nullable();
            $table->string('click_id')->nullable();
            $table->dateTime('datetime')->useCurrent();
            $table->text('data_in');
            $table->string('data_out')->nullable();
            $table->enum('status', ['new', 'sale', 'reject', 'NULL'])->nullable();
            $table->integer('result')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('api_log');
    }
}
