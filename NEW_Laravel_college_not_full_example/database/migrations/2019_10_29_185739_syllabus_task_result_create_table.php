<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SyllabusTaskResultCreateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('syllabus_task_result')) {
            Schema::create('syllabus_task_result', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('user_id');
                $table->unsignedInteger('syllabus_id');
                $table->unsignedInteger('student_discipline_id');
                $table->tinyInteger('payed')->default(0);
                $table->integer('value')->nullable(false);
                $table->integer('points')->nullable(false);

                $table->timestamps();
                $table->softDeletes();

                $table->foreign('user_id')->references('id')->on('users');
                $table->foreign('syllabus_id')->references('id')->on('syllabus');

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
        Schema::dropIfExists('syllabus_task_result');
    }
}
