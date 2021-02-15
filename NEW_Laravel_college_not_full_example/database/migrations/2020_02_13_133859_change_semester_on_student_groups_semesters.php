<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ChangeSemesterOnStudentGroupsSemesters extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE `student_groups_semesters` CHANGE `semester` `semester` CHAR(9) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL; ');

        DB::statement('UPDATE `student_groups_semesters` SET `semester` = \'2019-20.2\' WHERE `semester` = \'2019.2\'');
        DB::statement('UPDATE `student_groups_semesters` SET `semester` = \'2019-20.1\' WHERE `semester` = \'2019.1\'');
    }

    public function down()
    {
        DB::statement('UPDATE `student_groups_semesters` SET `semester` = \'2019.2\' WHERE `semester` = \'2019-20.2\'');
        DB::statement('UPDATE `student_groups_semesters` SET `semester` = \'2019.1\' WHERE `semester` = \'2019-20.1\'');

        DB::statement('ALTER TABLE `student_groups_semesters` CHANGE `semester` `semester` CHAR(6) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL; ');
    }
}