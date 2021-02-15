<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeighnsToPayDocuments extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE `pay_documents` CHANGE `user_id` `user_id` INT UNSIGNED NOT NULL; ');

        Schema::table(
            'pay_documents',
            function (Blueprint $table) {
                $table->dropIndex('user_index');

                $table->foreign('user_id')->on('users')->references('id');
            }
        );
    }

    public function down()
    {
        Schema::table(
            'pay_documents',
            function (Blueprint $table) {
                $table->dropForeign('pay_documents_user_id_foreign');

                $table->index('user_id', 'user_index');
            }
        );

         DB::statement('ALTER TABLE `pay_documents` CHANGE `user_id` `user_id` INT  NOT NULL; ');
    }
}