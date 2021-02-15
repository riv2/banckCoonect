<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SetSemesterNullableToStudentsDisciplineSemesterRatingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('students_discipline_semester_rating', function (Blueprint $table) {
            $table->string('semester')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('students_discipline_semester_rating', function (Blueprint $table) {
//            $table->string('semester')->nullable(false)->change();
        });
    }
}
