<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePollsQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('polls_questions')) {
            Schema::create('polls_questions', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('poll_id');
                $table->string('text_ru');
                $table->string('text_kz');
                $table->boolean('is_multiple');
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
        Schema::dropIfExists('polls_questions');
    }
}
