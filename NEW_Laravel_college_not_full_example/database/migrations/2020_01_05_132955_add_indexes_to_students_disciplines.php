<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddIndexesToStudentsDisciplines extends Migration
{
    public function up()
    {
        DB::statement('DELETE FROM `students_disciplines` WHERE student_id IN (153, 14158) ');

        DB::statement('ALTER TABLE `students_disciplines` CHANGE `student_id` `student_id` INT UNSIGNED NOT NULL; ');
//
        DB::statement('DELETE FROM `students_disciplines` WHERE discipline_id = 627 ');

        Schema::table(
            'students_disciplines',
            function (Blueprint $table) {
                $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('discipline_id')->references('id')->on('disciplines')->onDelete('cascade');
                $table->index(['student_id', 'discipline_id']);
            }
        );
    }

    public function down()
    {
        DB::statement('ALTER TABLE `students_disciplines` CHANGE `student_id` `student_id` INT NOT NULL; ');

        Schema::table(
            'students_disciplines',
            function (Blueprint $table) {
                $table->dropForeign('students_disciplines_student_id_foreign');
                 $table->dropForeign('students_disciplines_discipline_id_foreign');
                 $table->dropIndex('students_disciplines_student_id_discipline_id_index');
            }
        );


    }
}