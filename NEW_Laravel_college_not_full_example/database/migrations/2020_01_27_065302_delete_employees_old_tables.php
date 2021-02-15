<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteEmployeesOldTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('employees_user_educations');
        Schema::dropIfExists('employees_user_publications');
        Schema::dropIfExists('employees_user_teachers');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('employees_user_educations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('education_degree');
            $table->string('institution');
            $table->string('start_education');
            $table->string('end_education');
            $table->string('qualification_assigned');
            $table->string('protocol_number');
            $table->string('dissertation_topic');
            $table->string('nostrification');
            $table->timestamps();
        });
        Schema::create('employees_user_teachers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->date('teacher_start_date');
            $table->date('teacher_end_date');
            $table->string('job');
            $table->string('experience_type');
            $table->string('part_time_job');
            $table->timestamps();
        });
        Schema::create('employees_user_publications', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('theme');
            $table->string('science_branch');
            $table->text('content');
            $table->date('publication_date');
            $table->string('publication_name');
            $table->string('info');
            $table->string('impact_factor');
            $table->timestamps();
        });
    }
}
