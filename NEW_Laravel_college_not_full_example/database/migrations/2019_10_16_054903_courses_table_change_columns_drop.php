<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CoursesTableChangeColumnsDrop extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('description');
            $table->dropColumn('language');
            $table->dropColumn('certificate_file_name');
            $table->dropColumn('hours');
            $table->dropColumn('form_holding');
            $table->dropColumn('training_group');
            $table->dropColumn('scheme_courses_file');
            $table->dropColumn('scheme_courses_link');
            $table->dropColumn('trial_course_file');
            $table->dropColumn('trial_course_link');
            $table->dropColumn('is_certificate');
            $table->dropColumn('user_name');
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
            $table->text('description')->nullable(false);
            $table->string('language',255)->nullable(false);
            $table->string('certificate_file_name',255)->nullable(true);
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
            $table->enum('is_certificate',[
                'yes',
                'no',
            ])->default('no');
            $table->string('user_name',255)->nullable(true);
        });
    }
}
