<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('usertype', 20);
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password', 60);
            $table->string('phone')->nullable();
            $table->text('about')->nullable();
            $table->string('facebook')->nullable();
            $table->string('insta')->nullable();
            $table->string('image_icon')->nullable();
            $table->integer('status')->nullable();
            $table->string('confirmation_code')->nullable();
            $table->integer('trajectory_id');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
