<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeesUserTeachersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('employees_user_teachers')) {
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
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employees_user_teachers');
    }
}
