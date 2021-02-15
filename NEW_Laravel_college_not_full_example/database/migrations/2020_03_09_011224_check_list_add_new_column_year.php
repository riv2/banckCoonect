<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CheckListAddNewColumnYear extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if ( !Schema::hasColumn('check_list', 'year') ) {
            Schema::table('check_list', function (Blueprint $table)
            {
                $table->string('year',6)->nullable(true)->after('education_level');
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
        if ( Schema::hasColumn('check_list', 'year') ) {
            Schema::table('check_list', function (Blueprint $table)
            {
                $table->dropColumn('year');
            });
        }
    }
}
