<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentPublicationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_publications', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('mg_application_id');
            $table->foreign('mg_application_id')->references('id')->on('mg_applications')->onDelete('cascade');

            $table->string('type');
            $table->string('name');
            $table->string('place');
            $table->integer('year');
            $table->string('issue_number')->nullable();
            $table->string('file_name');
            $table->string('colleagues')->nullable();
            $table->string('lang');
            $table->string('isbn')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('student_publications');
    }
}
