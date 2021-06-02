<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRateRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rate_rules', function (Blueprint $table) {
            $table->id('id');
            $table->foreignId('partner_id')->nullable()->index('partner_id')->comment('id партнера');
            $table->foreignId('user_cat')->nullable()->comment('id категории партнера');
            $table->foreignId('business_unit_id')->nullable()->index('business_unit_id')->comment('id БЮ');
            $table->decimal('fee', 10)->nullable()->comment('Ставка для БЮ');
            $table->decimal('fee_advert', 10)->nullable();
            $table->foreignId('pp_id')->index();
            $table->foreignId('offer_id')->index();
            $table->dateTime('date_start')->comment('Дата старта');
            $table->dateTime('date_end')->nullable()->comment('Дата окончания');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rate_rules');
    }
}
