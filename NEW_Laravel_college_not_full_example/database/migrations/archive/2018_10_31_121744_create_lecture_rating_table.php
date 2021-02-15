<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLectureRatingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lecture_rating', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('lecture_id');
            $table->foreign('lecture_id')->references('id')->on('lectures');

            $table->unsignedInteger('user_id')->comment('Автор отзыва');
            $table->foreign('user_id')->references('id')->on('users');

            $table->float('value')->default(0);

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
        Schema::dropIfExists('lecture_rating');
    }
}
