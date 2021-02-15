<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeesUserEducationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('employees_user_educations')) {
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
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employees_user_educations');
    }
}
