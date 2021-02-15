<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntranceTestQuizeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entrance_test_quize', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('entrance_test_id');
            $table->foreign('entrance_test_id')->references('id')->on('entrance_test');

            $table->integer('quize_question_id');
            $table->foreign('quize_question_id')->references('id')->on('quize_questions');

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
        Schema::dropIfExists('entrance_test_quize');
    }
}
