<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMailLoggerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mail_logger', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->json('recipients');
            $table->json('sender');
            $table->string('subject');
            $table->text('message');
            $table->enum('status', ['new', 'sent', 'error']);
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
        Schema::dropIfExists('mail_logger');
    }
}
