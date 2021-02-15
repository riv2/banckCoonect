<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ChangeUserAgentAtAudits extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE `audits` CHANGE `user_agent` `user_agent` VARCHAR(600) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL; ');
    }

    public function down()
    {
        DB::statement('ALTER TABLE `audits` CHANGE `user_agent` `user_agent` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL; ');
    }
}