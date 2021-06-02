<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('links', function (Blueprint $table) {
            $table->id('id');
            $table->foreignId('pp_id');
            $table->foreignId('partner_id');
            $table->string('link_name');
            $table->string('link');
            $table->foreignId('link_source')->nullable();
            $table->foreignId('offer_id')->nullable();
            $table->enum('status', ['ACTIVE', 'DELETED'])->default('ACTIVE');
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
        Schema::dropIfExists('links');
    }
}
