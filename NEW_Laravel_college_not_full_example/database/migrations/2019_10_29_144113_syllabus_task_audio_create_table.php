<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SyllabusTaskAudioCreateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('syllabus_task_audio')) {
            Schema::create('syllabus_task_audio', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('question_id');
                $table->string('filename')->nullable(false);
                $table->string('origin_filename')->nullable(false);

                $table->timestamps();
                $table->softDeletes();

                $table->foreign('question_id')->references('id')->on('syllabus_task_questions')->onDelete('cascade');

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
        Schema::dropIfExists('syllabus_task_audio');
    }
}
