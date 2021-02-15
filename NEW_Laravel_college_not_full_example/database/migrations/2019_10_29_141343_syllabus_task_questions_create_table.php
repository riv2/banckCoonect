<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SyllabusTaskQuestionsCreateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('syllabus_task_questions')) {
            Schema::create('syllabus_task_questions', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('task_id');
                $table->integer('points')->default(0);
                $table->longtext('question')->nullable(false);

                $table->timestamps();
                $table->softDeletes();

                $table->foreign('task_id')->references('id')->on('syllabus_task')->onDelete('cascade');

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
        Schema::dropIfExists('syllabus_task_questions');
    }
}
