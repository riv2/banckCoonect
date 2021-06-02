<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChangesLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('changes_log', function (Blueprint $table) {
            $table->id('log_id');
            $table->foreignId('user_id');
            $table->string('record_type', 128);
            $table->enum('changes_type', ['new', 'update', 'delete']);
            $table->json('raw_data_old');
            $table->json('raw_data_new');
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
        Schema::dropIfExists('changes_log');
    }
}
