<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewBannedFraudsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banned_frauds', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->index();
            $table->foreign('order_id')->references('order_id')->on('orders');
            $table->foreignId('offer_id')->index();
            $table->text('comment')->nullable();
            $table->text('evidence')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['order_id', 'offer_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('banned_frauds');
    }
}
