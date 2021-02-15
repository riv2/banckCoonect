<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SyllabusTaskResultChangeColumnTaskId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('syllabus_task_result', function (Blueprint $table) {
            $table->dropColumn('syllabus_task_id');
            $table->integer('task_id')->nullable(false);
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
            $table->dropColumn('task_id');
            $table->integer('syllabus_task_id')->nullable(false);
        });
    }
}
