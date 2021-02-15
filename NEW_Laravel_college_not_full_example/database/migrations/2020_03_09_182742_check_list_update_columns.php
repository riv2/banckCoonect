<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CheckListUpdateColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if ( Schema::hasColumn('check_list', 'Interview_checked') ) {
            Schema::table('check_list', function (Blueprint $table)
            {
                $table->dropColumn('Interview_checked');
            });
        }
        if ( Schema::hasColumn('check_list', 'Interview_is_sum') ) {
            Schema::table('check_list', function (Blueprint $table)
            {
                $table->dropColumn('Interview_is_sum');
            });
        }
        if ( !Schema::hasColumn('check_list', 'interview_checked') ) {
            Schema::table('check_list', function (Blueprint $table)
            {
                $table->tinyInteger('interview_checked')->default(0)->after('prerequisites_is_sum');
            });
        }
        if ( !Schema::hasColumn('check_list', 'interview_is_sum') ) {
            Schema::table('check_list', function (Blueprint $table)
            {
                $table->tinyInteger('interview_is_sum')->default(0)->after('interview_checked');
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
        if ( Schema::hasColumn('check_list', 'interview_checked') ) {
            Schema::table('check_list', function (Blueprint $table)
            {
                $table->dropColumn('interview_checked');
            });
        }
        if ( Schema::hasColumn('check_list', 'interview_is_sum') ) {
            Schema::table('check_list', function (Blueprint $table)
            {
                $table->dropColumn('interview_is_sum');
            });
        }
    }
}
