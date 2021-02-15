<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EntranceExamAddColumnYear extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('entrance_exam', 'year')) {
            Schema::table('entrance_exam',function (Blueprint $table) {
                    $table->string('year',6)->after('custom_checked_user_show');
                }
            );
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('entrance_exam', 'year')) {
            Schema::table('entrance_exam', function (Blueprint $table)
            {
                $table->dropColumn('year');
            });
        }
    }
}
