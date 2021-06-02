<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatDailyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stat_daily', function (Blueprint $table) {
            $table->date('date');
            $table->integer('partner_id');
            $table->integer('offer_id')->nullable();
            $table->integer('landing_id')->nullable();
            $table->integer('link_id')->nullable();
            $table->integer('clicks')->nullable()->default(0);
            $table->integer('orders')->default(0);
            $table->integer('approve')->nullable()->default(0);
            $table->integer('sale')->default(0);
            $table->integer('activated')->nullable()->default(0);
            $table->integer('transactioned')->nullable()->default(0);
            $table->integer('revenue')->nullable()->default(0);
            $table->timestamps();
            $table->unique(['date', 'partner_id', 'link_id'], 'date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stat_daily');
    }
}
