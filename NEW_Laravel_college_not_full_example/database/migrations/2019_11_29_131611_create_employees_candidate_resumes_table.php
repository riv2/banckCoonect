<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeesCandidateResumesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('employees_candidate_resumes')) {
            Schema::create('employees_candidate_resumes', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('resume_id');
                $table->integer('requirement_id');
                $table->text('content');
                $table->string('file')->nullable();
                $table->timestamps();
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
        Schema::dropIfExists('employees_candidate_resumes');
    }
}
