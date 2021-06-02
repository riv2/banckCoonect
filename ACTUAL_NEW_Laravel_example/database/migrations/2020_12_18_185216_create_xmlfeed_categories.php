<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateXmlfeedCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('xmlfeed_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offer_material_id')->index();
            $table->foreignId('pp_id');
            $table->foreignId('category_id');
            $table->string('name');
            $table->unique(['pp_id', 'category_id', 'offer_material_id'], 'uniq_key');
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
        Schema::dropIfExists('xmlfeed_categories');
    }
}
