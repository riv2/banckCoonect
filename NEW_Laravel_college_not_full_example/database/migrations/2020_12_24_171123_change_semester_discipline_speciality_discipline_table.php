<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ChangeSemesterDisciplineSpecialityDisciplineTable extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE `speciality_discipline` CHANGE `semester` `semester` int(1) NULL;');
    }

    public function down()
    {
        DB::statement("ALTER TABLE `speciality_discipline` CHANGE `semester` `semester` set('1','2','3','4','5','6','7','8') COLLATE 'utf8_unicode_ci' NULL;");
    }
}