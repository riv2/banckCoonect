<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePayCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('pay_cards')) {
            Schema::create('pay_cards', function (Blueprint $table) {
                $table->increments('id');

                $table->unsignedInteger('user_id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

                $table->string('first_digits')->nullable();
                $table->string('last_digits')->nullable();
                $table->string('type')->description('Visa, mastercard и т.д.')->nullable();
                $table->string('exp_date')->nullable();
                $table->string('issuer')->nullable();
                $table->string('issuer_bank_country')->nullable();
                $table->string('token', 100)->nullable();

                $table->timestamps();
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
        Schema::dropIfExists('pay_cards');
    }
}
