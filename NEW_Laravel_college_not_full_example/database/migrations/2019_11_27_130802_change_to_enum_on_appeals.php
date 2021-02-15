<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeToEnumOnAppeals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE `appeals` MODIFY COLUMN `type` ENUM(\'test1\', \'sro\', \'exam\') NOT NULL ');
        DB::statement('ALTER TABLE `appeals` MODIFY COLUMN `status` ENUM(\'review\', \'approved\', \'declined\') NOT NULL ');
        DB::statement('ALTER TABLE `appeals` MODIFY COLUMN `resolution_action` ENUM(\'new_try\', \'add_value\') NULL ');

        DB::statement('ALTER TABLE `appeals` MODIFY COLUMN `expert1_resolution` ENUM(\'approved\', \'declined\')  NULL ');
        DB::statement('ALTER TABLE `appeals` MODIFY COLUMN `expert2_resolution` ENUM(\'approved\', \'declined\')  NULL ');
        DB::statement('ALTER TABLE `appeals` MODIFY COLUMN `expert3_resolution` ENUM(\'approved\', \'declined\')  NULL ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE `appeals` CHANGE `type` `type` VARCHAR(5) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL; ');
        DB::statement('ALTER TABLE `appeals` CHANGE `status` `status` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL; ');
        DB::statement('ALTER TABLE `appeals` CHANGE `resolution_action` `resolution_action` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci  NULL; ');

        DB::statement('ALTER TABLE `appeals` CHANGE `expert1_resolution` `expert1_resolution` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci  NULL; ');
        DB::statement('ALTER TABLE `appeals` CHANGE `expert2_resolution` `expert2_resolution` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci  NULL; ');
        DB::statement('ALTER TABLE `appeals` CHANGE `expert3_resolution` `expert3_resolution` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci  NULL; ');
    }
}
