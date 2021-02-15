<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePayDocumentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('pay_documents')) {
            Schema::create('pay_documents', function (Blueprint $table) {
                $table->increments('id');
                $table->bigInteger('order_id')->comment('Order number');
                $table->integer('user_id');
                //$table->foreign('user_id')->references('id')->on('users');
                $table->float('amount')->comment('Result sum');
                $table->enum('status', ['process', 'success', 'fail']);
                $table->text('hash')->comment('base64 for epay full answer')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pay_documents');
    }
}
