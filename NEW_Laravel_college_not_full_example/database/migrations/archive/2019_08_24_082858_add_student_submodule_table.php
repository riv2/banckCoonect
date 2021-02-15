<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStudentSubmoduleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_submodule', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('submodule_id');
            $table->foreign('submodule_id')->references('id')->on('submodules');

            $table->unsignedInteger('student_id');

            $table->timestamps();

            $table->index('student_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('student_submodule');
    }
}
