<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSyllabusTaskCoursePay extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('syllabus_task_course_pay')) {
            Schema::create('syllabus_task_course_pay', function (Blueprint $table) {
                $table->increments('id');

                $table->unsignedInteger('discipline_id');

                $table->unsignedInteger('user_id');
                $table->foreign('user_id')->references('id')->on('users');

                $table->enum('status', [
                    'process',
                    'ok',
                ])->default('process');

                $table->timestamps();
                $table->softDeletes();
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
        Schema::dropIfExists('syllabus_task_course_pay');
    }
}
