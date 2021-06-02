<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotifyParamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notify_params', function (Blueprint $table) {
            $table->unsignedInteger('partner_id')->primary();
            $table->enum('type', ['default', 'url4status'])->default('default');
            $table->string('postback_url', 512)->nullable();
            $table->string('postback_auth')->nullable();
            $table->enum('method', ['get', 'json', 'post'])->default('get');
            $table->string('status_new_value', 512)->nullable()->default('new');
            $table->string('status_approve_value', 512)->nullable()->default('approve');
            $table->string('status_sale_value', 512)->nullable()->default('sale');
            $table->string('status_reject_value', 512)->nullable()->default('reject');
            $table->string('status_transaction_value', 512)->nullable()->default('transaction');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notify_params');
    }
}
