<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdvertiserPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('advertiser_payments', function (Blueprint $table) {
            $table->id('payment_id');
            $table->integer('reestr_id')->nullable()->index('reestr_id');
            $table->integer('advertiser_id')->nullable();
            $table->string('advertiser')->nullable();
            $table->string('pay_method')->nullable();
            $table->string('pay_account')->nullable();
            $table->decimal('revenue', 12);
            $table->integer('status')->nullable()->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('advertiser_payments');
    }
}
