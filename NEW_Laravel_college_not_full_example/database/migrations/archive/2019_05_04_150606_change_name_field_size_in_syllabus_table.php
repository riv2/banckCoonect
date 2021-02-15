<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeNameFieldSizeInSyllabusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE syllabus CHANGE theme_name theme_name MEDIUMTEXT');
        DB::statement('ALTER TABLE syllabus CHANGE theme_name_en theme_name_en MEDIUMTEXT');
        DB::statement('ALTER TABLE syllabus CHANGE theme_name_kz theme_name_kz MEDIUMTEXT');
        DB::statement('ALTER TABLE syllabus CHANGE theme_name_ar theme_name_ar MEDIUMTEXT');
        DB::statement('ALTER TABLE syllabus CHANGE theme_name_de theme_name_de MEDIUMTEXT');
        DB::statement('ALTER TABLE syllabus CHANGE theme_name_fr theme_name_fr MEDIUMTEXT');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE syllabus CHANGE theme_name theme_name VARCHAR (255)');
        DB::statement('ALTER TABLE syllabus CHANGE theme_name_en theme_name_en VARCHAR (255)');
        DB::statement('ALTER TABLE syllabus CHANGE theme_name_kz theme_name_kz VARCHAR (255)');
        DB::statement('ALTER TABLE syllabus CHANGE theme_name_ar theme_name_ar VARCHAR (255)');
        DB::statement('ALTER TABLE syllabus CHANGE theme_name_de theme_name_de VARCHAR (255)');
        DB::statement('ALTER TABLE syllabus CHANGE theme_name_fr theme_name_fr VARCHAR (255)');
    }
}
