<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ChangeIndexOnMgApplications extends Migration
{
    public function up()
    {
        Schema::table(
            'mg_applications',
            function (Blueprint $table) {
                $table->dropForeign('mg_applications_user_id_foreign');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            }
        );
    }

    public function down()
    {
        Schema::table(
            'mg_applications',
            function (Blueprint $table) {
                $table->dropForeign('mg_applications_user_id_foreign');
                $table->foreign('user_id')->references('id')->on('users');
            }
        );
    }
}