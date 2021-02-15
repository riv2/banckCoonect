<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProfilesAddColumnsIp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if (!Schema::hasTable('user_business')) {
            Schema::create('user_business', function (Blueprint $table) {
                $table->increments('id');

                $table->unsignedInteger('user_id');
                $table->foreign('user_id')->references('id')->on('users');
                $table->string('name',255);
                $table->string('adress',255);
                $table->string('bin',255);
                $table->string('bank_name',255);
                $table->string('bank_bic',255);
                $table->string('iik',255);
                $table->string('kbe',255);
                $table->string('phone',255);

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
        Schema::dropIfExists('user_business');
    }
}
