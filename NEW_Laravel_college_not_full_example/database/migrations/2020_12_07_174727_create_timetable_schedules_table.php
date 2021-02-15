<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTimetableSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('timetable_schedules', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('discipline_id');
            $table->foreign('discipline_id')->on('disciplines')->references('id');
            $table->integer('teacher_id')->unsigned();
            $table->foreign('teacher_id')->on('users')->references('id');
            $table->integer('study_group_id')->unsigned();
            $table->foreign('study_group_id')->on('study_groups')->references('id');
            $table->string('semester');
            $table->date('date')->nullable();
            $table->string('lesson_type')->nullable();
            $table->string('topic')->nullable();
            $table->timestamps();
        });

        Schema::table('students_discipline_semester_rating', function (Blueprint $table) {
            $table->integer('timetable_schedules_id')->unsigned()->after('study_group_id');
            $table->foreign('timetable_schedules_id', 'sdsr_ts_foreign')->on('timetable_schedules')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('students_discipline_semester_rating', function (Blueprint $table) {
            $table->dropForeign('sdsr_ts_foreign');
            $table->dropColumn('timetable_schedules_id');
        });

        Schema::dropIfExists('timetable_schedules');
    }
}
