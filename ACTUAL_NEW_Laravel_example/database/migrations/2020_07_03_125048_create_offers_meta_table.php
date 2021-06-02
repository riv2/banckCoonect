<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOffersMetaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offers_meta', function (Blueprint $table) {
            $table->foreignId('offer_id')->index();
            $table->string('meta_name')->index();
            $table->json('meta_value');
            $table->timestamps();
            $table->primary(['offer_id', 'meta_name']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('offers_meta');
    }
}
