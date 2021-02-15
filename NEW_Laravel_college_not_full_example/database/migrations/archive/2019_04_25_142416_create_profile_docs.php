<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProfileDocs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profile_docs', function (Blueprint $table) {
            $table->increments('id');
            //$table->unsignedInteger('user_id');
            //$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('user_id');
            $table->string('filename', 255)->nullable();
            $table->string('doc_type', 64)->nullable();
            $table->boolean('check')->default(false);
            $table->boolean('last')->default(false);

            

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
        Schema::dropIfExists('profile_docs');
    }
}
