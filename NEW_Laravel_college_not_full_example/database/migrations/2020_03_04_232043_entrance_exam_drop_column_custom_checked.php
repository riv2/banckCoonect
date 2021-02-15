<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EntranceExamDropColumnCustomChecked extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if ( Schema::hasColumn('entrance_exam', 'custom_checked') ) {
            Schema::table('entrance_exam', function (Blueprint $table)
            {
                $table->dropColumn('custom_checked');
            });
        }
        if ( Schema::hasColumn('entrance_exam', 'exams_date_active') ) {
            Schema::table('entrance_exam', function (Blueprint $table)
            {
                $table->dropColumn('exams_date_active');
            });
        }
        if ( Schema::hasColumn('entrance_exam', 'exams_date_user_show') ) {
            Schema::table('entrance_exam', function (Blueprint $table)
            {
                $table->dropColumn('exams_date_user_show');
            });
        }
        if ( Schema::hasColumn('entrance_exam', 'exams_date_employee_show') ) {
            Schema::table('entrance_exam', function (Blueprint $table)
            {
                $table->dropColumn('exams_date_employee_show');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
