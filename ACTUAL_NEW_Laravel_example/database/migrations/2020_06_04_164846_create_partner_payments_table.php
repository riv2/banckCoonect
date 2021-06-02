<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartnerPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partner_payments', function (Blueprint $table) {
            $table->id('payment_id');
            $table->foreignId('reestr_id')->nullable();
            $table->foreignId('partner_id')->nullable();
            $table->foreignId('pp_id');
            $table->string('partner')->nullable();
            $table->string('pay_method')->nullable();
            $table->string('pay_account')->nullable();
            $table->decimal('revenue', 12);
            $table->boolean('status')->nullable()->default(0);
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
        Schema::dropIfExists('partner_payments');
    }
}
