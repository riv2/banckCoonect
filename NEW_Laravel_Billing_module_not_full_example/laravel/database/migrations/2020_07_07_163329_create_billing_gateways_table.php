<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillingGatewaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('_billing_gateways');

        Schema::create('_billing_gateways', function (Blueprint $table) {
            $table->string('uniq', 20)->primary();
            $table->string('name', 255);
            $table->string('date_created', 30);
            $table->string('date_updated', 30);
            $table->text('uniqs_currencies');
            $table->text('advanced');
            $table->text('settings');
            $table->string('enabled', 1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('_billing_gateways');
    }
}
