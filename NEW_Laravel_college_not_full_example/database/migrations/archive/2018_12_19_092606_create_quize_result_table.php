<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuizeResultTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quize_result', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('user_id')->comment('Student id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->integer('discipline_id');
            $table->foreign('discipline_id')->references('id')->on('disciplines')->onDelete('cascade');

            $table->boolean('payed')->default(false);

            $table->integer('value');

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
        Schema::dropIfExists('quize_result');
    }
}
