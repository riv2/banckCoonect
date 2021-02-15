<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class QuizResultPointTypeChange extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE `quize_result` CHANGE `points` `points` TINYINT UNSIGNED NULL DEFAULT NULL COMMENT \'Оценка в баллах\'; ');
    }

    public function down()
    {
        DB::statement('ALTER TABLE `quize_result` CHANGE `points` `points` DOUBLE(8,2) UNSIGNED NULL DEFAULT NULL COMMENT \'Оценка в баллах\'; ');
    }
}