<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewTableAgitatorRefunds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('agitator_refunds')) {
            Schema::create('agitator_refunds', function (Blueprint $table) {
                $table->increments('id');

                $table->unsignedInteger('user_id');
                $table->foreign('user_id')->references('id')->on('users');

                $table->unsignedInteger('bank_id');
                $table->foreign('bank_id')->references('id')->on('banks_list');

                $table->unsignedInteger('refunds_id');
                $table->foreign('refunds_id')->references('id')->on('refunds_list');

                $table->string('iban',255);
                $table->integer('cost');

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
        Schema::dropIfExists('agitator_refunds');
    }
}
