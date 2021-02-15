<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudyGroupTeacherTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('study_group_teacher')) {
            Schema::create('study_group_teacher', function (Blueprint $table) {
                $table->increments('id');

                $table->unsignedInteger('user_id')->comment('Teacher user id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

                $table->unsignedInteger('study_group_id');
                $table->foreign('study_group_id')->references('id')->on('study_groups')->onDelete('cascade');

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
        Schema::dropIfExists('study_group_teacher');
    }
}
