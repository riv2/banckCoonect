<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExQuizResultAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ex_quiz_result_answers', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('position');
            $table->unsignedInteger('result');
            $table->unsignedInteger('question');
            $table->unsignedInteger('answer')->nullable();

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
        Schema::dropIfExists('ex_quiz_result_answers');
    }
}
