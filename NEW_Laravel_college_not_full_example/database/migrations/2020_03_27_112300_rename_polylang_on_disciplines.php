<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class RenamePolylangOnDisciplines extends Migration
{
    public function up()
    {
        DB::statement('UPDATE disciplines SET polylang = 0');

        DB::statement('ALTER TABLE `disciplines` CHANGE `polylang` `tests_lang_invert` TINYINT(1) NOT NULL DEFAULT \'0\'; ');
    }

    public function down()
    {
        DB::statement('ALTER TABLE `disciplines` CHANGE `tests_lang_invert` `polylang` TINYINT(1) NULL; ');
    }
}