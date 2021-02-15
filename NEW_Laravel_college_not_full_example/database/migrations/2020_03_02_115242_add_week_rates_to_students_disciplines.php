<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddWeekRatesToStudentsDisciplines extends Migration
{
    public function up()
    {
        Schema::table(
            'students_disciplines',
            function (Blueprint $table) {
                $table->unsignedTinyInteger('week1_result')->nullable()->after('discipline_id');
                $table->unsignedTinyInteger('week2_result')->nullable()->after('week1_result');
                $table->unsignedTinyInteger('week3_result')->nullable()->after('week2_result');
                $table->unsignedTinyInteger('week4_result')->nullable()->after('week3_result');
                $table->unsignedTinyInteger('week5_result')->nullable()->after('week4_result');
                $table->unsignedTinyInteger('week6_result')->nullable()->after('week5_result');
                $table->unsignedTinyInteger('week7_result')->nullable()->after('week6_result');

                $table->unsignedTinyInteger('week9_result')->nullable()->after('week7_result');
                $table->unsignedTinyInteger('week10_result')->nullable()->after('week9_result');
                $table->unsignedTinyInteger('week11_result')->nullable()->after('week10_result');
                $table->unsignedTinyInteger('week12_result')->nullable()->after('week11_result');
                $table->unsignedTinyInteger('week13_result')->nullable()->after('week12_result');
                $table->unsignedTinyInteger('week14_result')->nullable()->after('week13_result');
                $table->unsignedTinyInteger('week15_result')->nullable()->after('week14_result');
            }
        );
    }

    public function down()
    {
        Schema::table(
            'students_disciplines',
            function (Blueprint $table) {
                $table->dropColumn('week1_result');
                $table->dropColumn('week2_result');
                $table->dropColumn('week3_result');
                $table->dropColumn('week4_result');
                $table->dropColumn('week5_result');
                $table->dropColumn('week6_result');
                $table->dropColumn('week7_result');
                $table->dropColumn('week9_result');
                $table->dropColumn('week10_result');
                $table->dropColumn('week11_result');
                $table->dropColumn('week12_result');
                $table->dropColumn('week13_result');
                $table->dropColumn('week14_result');
                $table->dropColumn('week15_result');
            }
        );
    }
}