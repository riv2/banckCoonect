<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CoursesAddNewColumnsForAdminPanel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->text('title_card')->nullable(true);
            $table->string('author_resume_file',255)->nullable(true);
            $table->string('author_resume_link',255)->nullable(true);
            $table->integer('hours')->nullable(true);
            $table->enum('form_holding',[
                'fulltime',
                'online',
                'distant'
            ])->default('fulltime');
            $table->integer('training_group')->nullable(true);
            $table->string('scheme_courses_file',255)->nullable(true);
            $table->string('scheme_courses_link',255)->nullable(true);
            $table->string('trial_course_file',255)->nullable(true);
            $table->string('trial_course_link',255)->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('title_card');
            $table->dropColumn('author_resume_file');
            $table->dropColumn('author_resume_link');
            $table->dropColumn('hours');
            $table->dropColumn('form_holding');
            $table->dropColumn('training_group');
            $table->dropColumn('scheme_courses_file');
            $table->dropColumn('scheme_courses_link');
            $table->dropColumn('trial_course_file');
            $table->dropColumn('trial_course_link');
        });
    }
}
