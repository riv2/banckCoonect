<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeTestResultPointsStudentsDisciplines extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE `students_disciplines` CHANGE `test_result_points` `test_result_points` TINYINT(3) UNSIGNED NULL DEFAULT NULL;');
        DB::statement('ALTER TABLE `students_disciplines` CHANGE `final_result_points` `final_result_points` FLOAT(7,2) UNSIGNED NULL DEFAULT NULL;');
        DB::statement('ALTER TABLE `students_disciplines` CHANGE `final_result_gpi` `final_result_gpi` FLOAT(7,2) UNSIGNED NULL DEFAULT NULL;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE `students_disciplines` CHANGE `test_result_points` `test_result_points` DOUBLE(8,2)  NULL DEFAULT NULL;');
        DB::statement('ALTER TABLE `students_disciplines` CHANGE `final_result_points` `final_result_points` DOUBLE  NULL DEFAULT NULL;');
        DB::statement('ALTER TABLE `students_disciplines` CHANGE `final_result_gpi` `final_result_gpi` DOUBLE NULL DEFAULT NULL;');
    }
}
