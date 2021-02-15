<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeNullableInEmployeesUserTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employees_user_publications', function (Blueprint $table) {
            $table->string('theme')->nullable()->change();
            $table->string('science_branch')->nullable()->change();
            $table->text('content')->nullable()->change();
            $table->date('publication_date')->nullable()->change();
            $table->string('publication_name')->nullable()->change();
            $table->string('info')->nullable()->change();
            $table->string('impact_factor')->nullable()->change();
        });
        Schema::table('employees_user_teachers', function (Blueprint $table) {
            $table->date('teacher_start_date')->nullable()->change();
            $table->date('teacher_end_date')->nullable()->change();
            $table->string('job')->nullable()->change();
            $table->string('experience_type')->nullable()->change();
            $table->string('part_time_job')->nullable()->change();
        });
        Schema::table('employees_user_educations', function (Blueprint $table) {
            $table->string('education_degree')->nullable()->change();
            $table->string('institution')->nullable()->change();
            $table->string('start_education')->nullable()->change();
            $table->string('end_education')->nullable()->change();
            $table->string('qualification_assigned')->nullable()->change();
            $table->string('protocol_number')->nullable()->change();
            $table->string('dissertation_topic')->nullable()->change();
            $table->string('nostrification')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employees_user_publications', function (Blueprint $table) {
            $table->string('theme')->change();
            $table->string('science_branch')->change();
            $table->text('content')->change();
            $table->date('publication_date')->change();
            $table->string('publication_name')->change();
            $table->string('info')->change();
            $table->string('impact_factor')->change();
        });
        Schema::table('employees_user_teachers', function (Blueprint $table) {
            $table->date('teacher_start_date')->change();
            $table->date('teacher_end_date')->change();
            $table->string('job')->change();
            $table->string('experience_type')->change();
            $table->string('part_time_job')->change();
        });
        Schema::table('employees_user_educations', function (Blueprint $table) {
            $table->string('education_degree')->change();
            $table->string('institution')->change();
            $table->string('start_education')->change();
            $table->string('end_education')->change();
            $table->string('qualification_assigned')->change();
            $table->string('protocol_number')->change();
            $table->string('dissertation_topic')->change();
            $table->string('nostrification')->change();
        });
    }
}
