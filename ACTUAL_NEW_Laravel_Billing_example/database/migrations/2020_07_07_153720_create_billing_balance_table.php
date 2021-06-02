<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillingBalanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('_billing_balance');

        Schema::create('_billing_balance', function (Blueprint $table) {
            $table->integer('uid', 20);
            $table->string('uniq_member', 20)->nullable();
            $table->string('uniq_company', 20)->nullable();
            $table->float('balance');
            $table->string('date_updated', 30)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('_billing_balance');
    }
}
