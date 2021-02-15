<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AgitatorUsersStatusAddNewValue extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE `agitator_users` MODIFY COLUMN `status` ENUM(\'process\', \'ok\', \'error\', \'payed\')');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE `agitator_users` MODIFY COLUMN `status` ENUM(\'process\', \'ok\', \'error\')');
    }
}
