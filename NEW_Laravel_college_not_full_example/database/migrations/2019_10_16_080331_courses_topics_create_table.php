<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CoursesTopicsCreateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('courses_topics')) {
            Schema::create('courses_topics', function (Blueprint $table) {
                $table->increments('id');

                $table->unsignedInteger('courses_id');

                $table->text('description')->nullable(false);
                $table->string('language', 255)->nullable(false);
                $table->string('certificate_file_name', 255)->nullable(true);
                $table->integer('hours')->nullable(true);
                $table->enum('form_holding', [
                    'fulltime',
                    'online',
                    'distant'
                ])->default('fulltime');
                $table->integer('training_group')->nullable(true);
                $table->string('scheme_courses_file', 255)->nullable(true);
                $table->string('scheme_courses_link', 255)->nullable(true);
                $table->string('trial_course_file', 255)->nullable(true);
                $table->string('trial_course_link', 255)->nullable(true);
                $table->enum('is_certificate', [
                    'yes',
                    'no',
                ])->default('no');

                $table->timestamps();
                $table->softDeletes();

                $table->foreign('courses_id')->references('id')->on('courses')->onDelete('cascade');

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
        Schema::dropIfExists('courses_topics');
    }
}
