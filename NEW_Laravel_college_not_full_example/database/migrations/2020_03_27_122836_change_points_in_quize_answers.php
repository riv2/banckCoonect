<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ChangePointsInQuizeAnswers extends Migration
{
    public function up()
    {
        DB::statement('UPDATE `quize_answers` SET `points` = ABS(`points`)');

        DB::statement('ALTER TABLE `quize_answers` CHANGE `points` `points` TINYINT UNSIGNED NOT NULL DEFAULT \'0\' COMMENT \'correct have points\'');
    }

    public function down()
    {
        DB::statement('ALTER TABLE `quize_answers` CHANGE `points` `points` INT NOT NULL DEFAULT \'0\' COMMENT \'correct have points\'; ');
    }
}