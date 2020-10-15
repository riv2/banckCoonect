<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillingProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('_billing_products');

        Schema::create('_billing_products', function (Blueprint $table) {
            $table->integer('uid', 20);
            $table->string('name', 255);
            $table->string('date_created', 30);
            $table->string('date_updated', 30);
            $table->string('article', 255)->nullable();
            $table->text('advanced')->nullable();
            $table->string('type_product', 20)->nullable();
            $table->string('table', 255)->nullable();
            $table->string('uniq_table', 255)->nullable();
            $table->string('status', 255)->nullable();
            $table->string('code', 255)->nullable();
            $table->string('advanced_value', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('_billing_products');
    }
}
