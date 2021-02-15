<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddTest1ZeroedByTimeInStudentsDisciplines extends Migration
{
    public function up()
    {
        Schema::table(
            'students_disciplines',
            function (Blueprint $table) {
                $table->boolean('test1_zeroed_by_time')->default(0)->after('test1_blur');
            }
        );
    }

    public function down()
    {
        Schema::table(
            'students_disciplines',
            function (Blueprint $table) {
                $table->dropColumn('test1_zeroed_by_time');
            }
        );
    }
}