<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePixelLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pixel_log', function (Blueprint $table) {
            $table->id('id');
            $table->foreignId('pp_id');
            $table->json('data');
            $table->string('ip', 20)->nullable();
            $table->integer('is_valid')->nullable();
            $table->boolean('is_click')->nullable();
            $table->boolean('is_order')->nullable();
            $table->text('status')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pixel_log');
    }
}
