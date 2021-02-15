<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SyllabusTaskAddNewTypeItem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE `syllabus_task` MODIFY COLUMN `type` ENUM(\'text\', \'img\', \'link\', \'audio\', \'video\', \'event\', \'essay\') NULL ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE `syllabus_task` MODIFY COLUMN `type` ENUM(\'text\', \'img\', \'link\', \'audio\', \'video\', \'event\') NULL ');
    }
}
