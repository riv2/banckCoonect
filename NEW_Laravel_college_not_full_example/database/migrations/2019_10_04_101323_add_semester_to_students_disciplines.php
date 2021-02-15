<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSemesterToStudentsDisciplines extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('students_disciplines', function (Blueprint $table) {
            $table->unsignedTinyInteger('semester')->after('migrated')->nullable()->comment('Semester by study plan');
            $table->integer('at_semester')->nullable()->comment('Bought at semester')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('students_disciplines', function (Blueprint $table) {
            $table->dropColumn('semester');
            $table->integer('at_semester')->nullable()->comment('')->change();
        });
    }
}
