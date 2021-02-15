<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ForeignUserIdToProfiles extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE `profiles` CHANGE `id` `id` INT UNSIGNED NOT NULL AUTO_INCREMENT; ');
        DB::statement('ALTER TABLE `profiles` CHANGE `user_id` `user_id` INT UNSIGNED NULL DEFAULT NULL; ');

        DB::statement('DELETE FROM `profiles` WHERE id in (137, 177, 11175)');

        Schema::table(
            'profiles',
            function (Blueprint $table) {
                $table->dropIndex('user_index');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            }
        );
    }

    public function down()
    {
        Schema::table(
            'profiles',
            function (Blueprint $table) {
                $table->dropForeign('profiles_user_id_foreign');
                $table->index('user_id', 'user_index');
            }
        );

        DB::statement('ALTER TABLE `profiles` CHANGE `user_id` `user_id` INT NOT NULL AUTO_INCREMENT; ');
        DB::statement('ALTER TABLE `profiles` CHANGE `id` `id` INT NOT NULL AUTO_INCREMENT; ');
    }
}
