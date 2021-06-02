<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogPixelClickTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_pixel_clicks', function (Blueprint $table) {
            $table->id();
            $table->json('data')->nullable();
            $table->string('referer', 2048)->nullable();
            $table->ipAddress('ip')->nullable();
            $table->boolean('is_valid')->nullable();
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
        Schema::dropIfExists('log_clickpixel');
    }
}
