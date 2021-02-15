<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuizeAudiofilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quize_audiofiles', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('quize_question_id');
            $table->foreign('quize_question_id')->references('id')->on('quize_questions')->onDelete('cascade');

            $table->string('filename');
            $table->string('original_filename');

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
        Schema::dropIfExists('quize_audiofiles');
    }
}
