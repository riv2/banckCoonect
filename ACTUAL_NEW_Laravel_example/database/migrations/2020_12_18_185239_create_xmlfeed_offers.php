<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateXmlfeedOffers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('xmlfeed_offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offer_material_id')->index();
            $table->foreignId('pp_id')->index();
            $table->foreignId('category_id')->index();
            $table->string('url');
            $table->json('xml_data');
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
        Schema::dropIfExists('xmlfeed_offers');
    }
}
