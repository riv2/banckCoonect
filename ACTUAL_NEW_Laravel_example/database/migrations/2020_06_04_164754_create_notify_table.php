<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotifyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notify', function (Blueprint $table) {
            $table->integer('notify_id', true);
            $table->dateTime('datetime')->useCurrent();
            $table->integer('partner_id')->nullable();
            $table->string('click_id')->nullable()->default('');
            $table->string('web_id')->nullable();
            $table->string('order_id')->nullable();
            $table->integer('link_id')->nullable();
            $table->string('link_postback_param')->nullable();
            $table->enum('model', ['approve', 'sale', 'lead', 'activation'])->nullable();
            $table->enum('status', ['new', 'approve', 'sale', 'reject', 'activation', 'transaction'])->nullable()->default('new');
            $table->integer('amount')->nullable();
            $table->string('comment')->nullable();
            $table->integer('sent_cnt')->default(0);
            $table->string('sent_url', 512)->nullable();
            $table->enum('sent_method', ['get', 'post', 'json'])->nullable();
            $table->text('sent_request')->nullable();
            $table->dateTime('sent_datetime')->nullable();
            $table->integer('responce_httpcode')->nullable()->index('http_code');
            $table->text('responce_body')->nullable();
            $table->unique(['order_id', 'status', 'sent_cnt'], 'unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notify');
    }
}
