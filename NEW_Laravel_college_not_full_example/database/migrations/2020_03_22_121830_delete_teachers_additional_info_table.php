<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class DeleteTeachersAdditionalInfoTable extends Migration
{
    public function up()
    {
        Schema::dropIfExists('teachers_additional_info');
    }

    public function down()
    {
        Schema::create('teachers_additional_info', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->references('id')->on('users');

            $table->string('cv')->nullable()->default(null);
            $table->string('facebook')->nullable()->default(null);
            $table->string('linkedin')->nullable()->default(null);

            $table->timestamps();
        });
    }
}