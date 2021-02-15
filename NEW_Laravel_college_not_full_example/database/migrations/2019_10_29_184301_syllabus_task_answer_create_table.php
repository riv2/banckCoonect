<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SyllabusTaskAnswerCreateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('syllabus_task_answer')) {
            Schema::create('syllabus_task_answer', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('question_id');
                $table->longText('answer')->nullable(true);
                $table->integer('points')->nullable(false);
                $table->tinyInteger('correct')->default(0);

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
        Schema::dropIfExists('syllabus_task_answer');
    }
}
