<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EntranceExamAddNewColumnsActive extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if ( !Schema::hasColumn('entrance_exam', 'manual_active') && !Schema::hasColumn('entrance_exam', 'statement_active') ) {
            Schema::table('entrance_exam', function (Blueprint $table)
            {
                $table->tinyInteger('manual_active')->default(0)->after('date_employee_show');
                $table->tinyInteger('manual_user_show')->default(0)->after('manual_active');
                $table->tinyInteger('manual_employee_show')->default(0)->after('manual_user_show');

                $table->tinyInteger('statement_active')->default(0)->after('manual_employee_show');
                $table->tinyInteger('statement_user_show')->default(0)->after('statement_active');
                $table->tinyInteger('statement_employee_show')->default(0)->after('statement_user_show');

                $table->tinyInteger('commission_structure_active')->default(0)->after('statement_employee_show');
                $table->tinyInteger('commission_structure_user_show')->default(0)->after('commission_structure_active');
                $table->tinyInteger('commission_structure_employee_show')->default(0)->after('commission_structure_user_show');

                $table->tinyInteger('composition_appeal_commission_active')->default(0)->after('commission_structure_employee_show');
                $table->tinyInteger('composition_appeal_commission_user_show')->default(0)->after('composition_appeal_commission_active');
                $table->tinyInteger('composition_appeal_commission_employee_show')->default(0)->after('composition_appeal_commission_user_show');

                $table->tinyInteger('schedule_active')->default(0)->after('composition_appeal_commission_employee_show');
                $table->tinyInteger('schedule_user_show')->default(0)->after('schedule_active');
                $table->tinyInteger('schedule_employee_show')->default(0)->after('schedule_user_show');

                $table->tinyInteger('protocols_creative_exams_active')->default(0)->after('schedule_employee_show');
                $table->tinyInteger('protocols_creative_exams_user_show')->default(0)->after('protocols_creative_exams_active');
                $table->tinyInteger('protocols_creative_exams_employee_show')->default(0)->after('protocols_creative_exams_user_show');

                $table->tinyInteger('protocols_appeal_commission_active')->default(0)->after('protocols_creative_exams_employee_show');
                $table->tinyInteger('protocols_appeal_commission_user_show')->default(0)->after('protocols_appeal_commission_active');
                $table->tinyInteger('protocols_appeal_commission_employee_show')->default(0)->after('protocols_appeal_commission_user_show');

                $table->tinyInteger('report_exams_active')->default(0)->after('protocols_appeal_commission_employee_show');
                $table->tinyInteger('report_exams_user_show')->default(0)->after('report_exams_active');
                $table->tinyInteger('report_exams_employee_show')->default(0)->after('report_exams_user_show');

                $table->tinyInteger('custom_checked_employee_show')->default(0)->after('custom_checked_user_show');

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
        if ( Schema::hasColumn('entrance_exam', 'manual_active') && Schema::hasColumn('entrance_exam', 'statement_active') ) {
            Schema::table('entrance_exam', function (Blueprint $table)
            {
                $table->dropColumn('manual_active');
                $table->dropColumn('manual_user_show');
                $table->dropColumn('manual_employee_show');

                $table->dropColumn('statement_active');
                $table->dropColumn('statement_user_show');
                $table->dropColumn('statement_employee_show');

                $table->dropColumn('commission_structure_active');
                $table->dropColumn('commission_structure_user_show');
                $table->dropColumn('commission_structure_employee_show');

                $table->dropColumn('composition_appeal_commission_active');
                $table->dropColumn('composition_appeal_commission_user_show');
                $table->dropColumn('composition_appeal_commission_employee_show');

                $table->dropColumn('schedule_active');
                $table->dropColumn('schedule_user_show');
                $table->dropColumn('schedule_employee_show');

                $table->dropColumn('protocols_creative_exams_active');
                $table->dropColumn('protocols_creative_exams_user_show');
                $table->dropColumn('protocols_creative_exams_employee_show');

                $table->dropColumn('protocols_appeal_commission_active');
                $table->dropColumn('protocols_appeal_commission_user_show');
                $table->dropColumn('protocols_appeal_commission_employee_show');

                $table->dropColumn('report_exams_active');
                $table->dropColumn('report_exams_user_show');
                $table->dropColumn('report_exams_employee_show');

                $table->dropColumn('custom_checked_employee_show');

            });
        }
    }
}
