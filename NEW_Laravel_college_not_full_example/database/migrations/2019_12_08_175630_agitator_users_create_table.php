<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AgitatorUsersCreateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('agitator_users')) {
            Schema::create('agitator_users', function (Blueprint $table) {
                $table->bigIncrements('id');

                $table->unsignedInteger('user_id')->nullable(false);
                $table->unsignedInteger('stud_id')->nullable(false);

                $table->foreign('user_id')->references('id')->on('users');
                $table->foreign('stud_id')->references('id')->on('users');

                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('agitator_users');
    }
}
