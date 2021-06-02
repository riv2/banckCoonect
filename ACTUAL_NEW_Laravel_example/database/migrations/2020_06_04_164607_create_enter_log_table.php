<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEnterLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('enter_log', function (Blueprint $table) {
            $table->integer('enter_id', true);
            $table->dateTime('datetime')->nullable();
            $table->integer('user_id');
            $table->enum('result', ['success', 'fail', 'emailwait', 'blocked', 'moderation'])->nullable()->default('fail');
            $table->string('ip', 20);
            $table->string('ua')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('enter_log');
    }
}
