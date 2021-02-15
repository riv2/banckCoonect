<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FinalGPAStudentsDisciplines extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE `students_disciplines` CHANGE `final_result_gpa` `final_result_gpa` DOUBLE(3,2) UNSIGNED NULL DEFAULT NULL; ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE `students_disciplines` CHANGE `final_result_gpa` `final_result_gpa` DOUBLE NULL DEFAULT NULL;');
    }
}
