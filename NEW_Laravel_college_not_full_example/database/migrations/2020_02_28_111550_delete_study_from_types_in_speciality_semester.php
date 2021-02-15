<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class DeleteStudyFromTypesInSpecialitySemester extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE `speciality_semester` CHANGE `type` `type` ENUM(\'plan_approval\',\'buying\',\'buy_cancel\',\'syllabuses\',\'test1\',\'test1_retake\',\'sro\',\'sro_retake\',\'exam\',\'exam_retake\') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL; ');
    }

    public function down()
    {
        DB::statement('ALTER TABLE `speciality_semester` CHANGE `type` `type` ENUM(\'study\', \'plan_approval\',\'buying\',\'buy_cancel\',\'syllabuses\',\'test1\',\'test1_retake\',\'sro\',\'sro_retake\',\'exam\',\'exam_retake\') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL; ');
    }
}