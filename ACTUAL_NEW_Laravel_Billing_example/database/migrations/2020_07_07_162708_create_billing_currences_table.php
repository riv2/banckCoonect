<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillingCurrencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('_billing_currences');

        Schema::create('_billing_currences', function (Blueprint $table) {
            $table->string('uniq', 20)->primary();
            $table->string('name', 20);
            $table->float('ratio');
            $table->string('code', 3);
            $table->string('date_created', 30);
            $table->string('date_updated', 30);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('_billing_currences');
    }
}
