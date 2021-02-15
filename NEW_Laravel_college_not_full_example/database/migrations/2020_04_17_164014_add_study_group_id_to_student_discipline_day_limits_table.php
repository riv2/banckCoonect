<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStudyGroupIdToStudentDisciplineDayLimitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('student_discipline_day_limits', function (Blueprint $table) {
            $table->unsignedInteger('study_group_id')->nullable()->after('teacher_id');
            $table->foreign('study_group_id')->references('id')->on('study_groups')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('student_discipline_day_limits', function (Blueprint $table) {
            $table->dropForeign('student_discipline_day_limits_study_group_id_foreign');
            $table->dropColumn('study_group_id');
        });
    }
}
