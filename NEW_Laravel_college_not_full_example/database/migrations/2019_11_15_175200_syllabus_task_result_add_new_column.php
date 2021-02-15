<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SyllabusTaskResultAddNewColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('syllabus_task_result', function (Blueprint $table) {
            $table->integer('syllabus_task_id')->nullable(false);
            $table->dropColumn('student_discipline_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('syllabus_task_result', function (Blueprint $table) {
            $table->integer('student_discipline_id')->nullable(false);
            $table->dropColumn('syllabus_task_id');
        });
    }
}
