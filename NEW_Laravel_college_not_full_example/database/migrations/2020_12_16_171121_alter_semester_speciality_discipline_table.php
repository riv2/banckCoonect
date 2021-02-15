<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSemesterSpecialityDisciplineTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `speciality_discipline` CHANGE `semester` `semester` set('1','2','3','4','5','6','7','8') COLLATE 'utf8_unicode_ci' NULL;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE `speciality_discipline` CHANGE `semester` `semester` integer(1) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL; ');
    }
}