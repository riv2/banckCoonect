<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SetDisciplineSectorIdNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `disciplines` CHANGE `sector_id` `sector_id` INT COLLATE 'utf8_unicode_ci' NULL;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE `disciplines` CHANGE `sector_id` `sector_id` INT COLLATE 'utf8_unicode_ci' NOT NULL;");
    }
}
