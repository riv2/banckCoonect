<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExQuizResultTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ex_quiz_result', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('schedule')->nullable();
            $table->unsignedInteger('student')->nullable();
            $table->dateTime('begin_time');
            $table->unsignedInteger('finished');
            $table->boolean('deleted');
            $table->unsignedInteger('userId')->nullable();
            $table->unsignedInteger('plan_item')->nullable();
            $table->unsignedInteger('quiz_num')->nullable();
            $table->unsignedInteger('writeoff_id')->nullable();
            $table->unsignedInteger('plan_item_discipline_id')->nullable();
            $table->unsignedInteger('temp_user')->nullable();

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
        Schema::dropIfExists('ex_quiz_result');
    }
}
