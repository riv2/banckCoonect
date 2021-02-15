<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeRemoteAccessOnStudentsDisciplines extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('students_disciplines', function (Blueprint $table) {
            $table->dropColumn('test1_remote');
            $table->boolean('remote_access')->default(0)->after('free_credits');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('students_disciplines', function (Blueprint $table) {
            $table->dropColumn('remote_access');
            $table->boolean('test1_remote')->after('test1_result_trial')->nullable();
        });
    }
}
