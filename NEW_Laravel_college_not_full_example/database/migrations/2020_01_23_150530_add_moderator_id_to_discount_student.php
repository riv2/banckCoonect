<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddModeratorIdToDiscountStudent extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE `discount_student` CHANGE `user_id` `user_id` INT UNSIGNED NULL DEFAULT NULL; ');
        DB::statement('ALTER TABLE `discount_student` CHANGE `type_id` `type_id` INT UNSIGNED NULL DEFAULT NULL; ');
        DB::statement('DELETE FROM `discount_student` WHERE `discount_student`.`id` IN (1, 196)');

        Schema::table(
            'discount_student',
            function (Blueprint $table) {
                $table->unsignedInteger('moderator_id')->comment('User who made decision')->nullable()->after('date_approve');

                $table->foreign('moderator_id')->on('users')->references('id');
                $table->foreign('user_id')->on('users')->references('id')->onDelete('cascade');
                $table->foreign('type_id')->on('discount_type_list')->references('id');
            }
        );
    }

    public function down()
    {
        Schema::table(
            'discount_student',
            function (Blueprint $table) {
                $table->dropForeign('discount_student_moderator_id_foreign');
                $table->dropForeign('discount_student_user_id_foreign');
                $table->dropForeign('discount_student_type_id_foreign');

                $table->dropColumn('moderator_id');
            }
        );

        DB::statement('ALTER TABLE `discount_student` CHANGE `user_id` `user_id` INT  NULL DEFAULT NULL; ');
        DB::statement('ALTER TABLE `discount_student` CHANGE `type_id` `type_id` INT  NULL DEFAULT NULL; ');
    }
}