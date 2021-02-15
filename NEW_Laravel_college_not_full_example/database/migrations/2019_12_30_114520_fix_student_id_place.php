<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class FixStudentIdPlace extends Migration
{
    public function up()
    {
        \Schema::table(
            'students_disciplines',
            function (Blueprint $table) {
                $table->integer('student_id_new')->after('id');
            }
        );

        DB::statement('UPDATE students_disciplines SET student_id_new = student_id');

        \Schema::table(
            'students_disciplines',
            function (Blueprint $table) {
                $table->dropColumn('student_id');
            }
        );

        DB::statement('ALTER TABLE students_disciplines CHANGE student_id_new student_id integer NOT NULL ;');
    }

    public function down()
    {

    }
}