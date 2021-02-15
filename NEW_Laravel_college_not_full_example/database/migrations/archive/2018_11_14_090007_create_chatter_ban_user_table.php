<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChatterBanUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chatter_ban_user', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('user_id')->comment('В отношении кого бан');
            $table->foreign('user_id')->references('id')->on('users');

            $table->unsignedInteger('initiator_id')->comment('Инициатор бана');
            $table->foreign('initiator_id')->references('id')->on('users');

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
        Schema::dropIfExists('chatter_ban_user');
    }
}
