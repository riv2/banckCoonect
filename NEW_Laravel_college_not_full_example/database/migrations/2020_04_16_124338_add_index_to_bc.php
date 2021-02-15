<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddIndexToBc extends Migration
{
    public function up()
    {
        DB::statement('DELETE FROM `bc_applications` WHERE NOT EXISTS (select * from users where `bc_applications`.`user_id` = users.id)');
        DB::statement('ALTER TABLE `bc_applications` CHANGE `user_id` `user_id` INT UNSIGNED NOT NULL; ');

        Schema::table('bc_applications', function (Blueprint $table) {
            $table->dropIndex('user_index');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('bc_applications', function (Blueprint $table) {
            $table->index('user_id', 'user_index');
            $table->dropForeign('bc_applications_user_id_foreign');
        });
    }
}