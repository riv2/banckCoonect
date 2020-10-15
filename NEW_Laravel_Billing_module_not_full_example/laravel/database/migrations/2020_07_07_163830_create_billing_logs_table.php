<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillingLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('_billing_logs');

        Schema::create('_billing_logs', function (Blueprint $table) {
            $table->integer('uid', 20);
            $table->string('requester', 255);
            $table->string('uniqMember', 255)->nullable();
            $table->string('status', 255);
            $table->text('data');
            $table->text('advanced')->nullable();
            $table->string('date_created', 30);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('_billing_logs');
    }
}
