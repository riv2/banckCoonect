<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddStudentGroupsSemestersTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('student_groups_semesters')) {
            Schema::create(
                'student_groups_semesters',
                function (Blueprint $table) {
                    $table->increments('id');

                    $table->unsignedInteger('user_id');
                    $table->foreign('user_id')->on('users')->references('id');

                    $table->unsignedInteger('study_group_id');
                    $table->foreign('study_group_id')->on('study_groups')->references('id');

                    $table->char('semester', 6);

                    $table->timestamps();
                }
            );
        }
    }

    public function down()
    {
        Schema::dropIfExists('student_groups_semesters');
    }
}