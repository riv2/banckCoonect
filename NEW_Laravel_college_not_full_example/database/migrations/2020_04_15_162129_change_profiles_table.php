<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ChangeProfilesTable extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE `profiles` CHANGE `front_id_photo` `front_id_photo` VARCHAR(128) CHARACTER SET utf8 COLLATE utf8_general_ci NULL; ');
        DB::statement('ALTER TABLE `profiles` CHANGE `back_id_photo` `back_id_photo` VARCHAR(128) CHARACTER SET utf8 COLLATE utf8_general_ci NULL; ');
    }

    public function down()
    {
        DB::statement('ALTER TABLE `profiles` CHANGE `front_id_photo` `front_id_photo` VARCHAR(128) CHARACTER SET utf8 COLLATE utf8_general_ci ; ');
        DB::statement('ALTER TABLE `profiles` CHANGE `back_id_photo` `back_id_photo` VARCHAR(128) CHARACTER SET utf8 COLLATE utf8_general_ci ; ');
    }
}