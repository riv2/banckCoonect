<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteEmployeesUselessTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('employees_vacancy_requirements');
        Schema::dropIfExists('employees_candidate_requirements');
        Schema::dropIfExists('employees_candidate_resumes');
        Schema::dropIfExists('employees_position_resumes');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('employees_vacancy_requirements', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('vacancy_id');
            $table->integer('requirement_id');
            $table->timestamps();
        });
        Schema::create('employees_candidate_requirements', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('description');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('file')->default(false);
            $table->timestamps();
        });
        Schema::create('employees_candidate_resumes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('resume_id');
            $table->integer('requirement_id');
            $table->text('content');
            $table->string('file')->nullable();
            $table->timestamps();
        });
        Schema::create('employees_position_resumes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('resume_id');
            $table->integer('requirement_id');
            $table->text('content');
            $table->string('file')->nullable();
            $table->timestamps();
        });
    }
}
