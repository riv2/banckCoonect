<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillingTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('_billing_transactions');

        Schema::create('_billing_transactions', function (Blueprint $table) {
            $table->integer('uid', 20);
            $table->string('uniq_member', 20);
            $table->string('uid_product', 20);
            $table->string('type_transaction', 20);
            $table->string('hide_transaction', 20)->nullable();
            $table->float('sum');
            $table->text('product_serialize')->nullable();
            $table->string('signature', 255)->nullable();
            $table->string('date_created', 30);
            $table->string('date', 30);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('_billing_transactions');
    }
}
