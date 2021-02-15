<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('order_name_id');
            $table->foreign('order_name_id')->references('id')->on('order_names')->onDelete('cascade');

            $table->string('number')->nullable();
            $table->date('date')->nullable();
            $table->string('npa')->nullable();

            $table->unsignedInteger('order_action_id');
            $table->foreign('order_action_id')->references('id')->on('order_actions')->onDelete('cascade');

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
        Schema::dropIfExists('orders');
    }
}
