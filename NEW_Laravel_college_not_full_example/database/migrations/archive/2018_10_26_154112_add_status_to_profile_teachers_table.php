<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusToProfileTeachersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('profile_teachers', function (Blueprint $table) {
            $table->enum('status', ['moderation', 'active', 'block'])->after('user_id')->default('moderation');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('profile_teachers', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
