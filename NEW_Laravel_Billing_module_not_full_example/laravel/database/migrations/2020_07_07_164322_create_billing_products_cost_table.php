<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillingProductsCostTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('_billing_products_cost');

        Schema::create('_billing_products_cost', function (Blueprint $table) {
            $table->integer('uid', 20);
            $table->string('uid_product', 20)->nullable();
            $table->string('uniq_currency', 20);
            $table->string('date_created', 30);
            $table->string('date_updated', 30);
            $table->string('article', 255)->nullable();
            $table->float('cost');
            $table->string('count', 20);
            $table->text('advanced')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('_billing_products_cost');
    }
}
