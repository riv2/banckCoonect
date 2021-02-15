<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddFinalResultDate extends Migration
{
    public function up()
    {
        \Schema::table(
            'students_disciplines',
            function (Blueprint $table) {
                $table->dateTime('final_date')->after('final_result_letter')->nullable();
            }
        );
    }

    public function down()
    {
        \Schema::table('students_disciplines',
            function (Blueprint $table) {
                $table->dropColumn('final_date');
            }
        );
    }
}