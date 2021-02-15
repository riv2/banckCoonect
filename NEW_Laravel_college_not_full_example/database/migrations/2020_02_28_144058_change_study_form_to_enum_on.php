<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ChangeStudyFormToEnumOn extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE `semesters` CHANGE `study_form` `study_form` ENUM(\'fulltime\',\'online\',\'evening\',\'extramural\') NOT NULL; ');
    }

    public function down()
    {
        DB::statement('ALTER TABLE `semesters` CHANGE `study_form` `study_form` VARCHAR(15) NULL; ');
    }
}