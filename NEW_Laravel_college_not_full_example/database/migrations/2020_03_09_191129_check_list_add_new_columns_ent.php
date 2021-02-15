<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CheckListAddNewColumnsEnt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if ( !Schema::hasColumn('check_list', 'ent_checked') ) {
            Schema::table('check_list', function (Blueprint $table)
            {
                $table->tinyInteger('ent_checked')->default(0)->after('interview_is_sum');
            });
        }
        if ( !Schema::hasColumn('check_list', 'ent_is_sum') ) {
            Schema::table('check_list', function (Blueprint $table)
            {
                $table->tinyInteger('ent_is_sum')->default(0)->after('ent_checked');
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
        if ( Schema::hasColumn('check_list', 'ent_checked') ) {
            Schema::table('check_list', function (Blueprint $table)
            {
                $table->dropColumn('ent_checked');
            });
        }
        if ( Schema::hasColumn('check_list', 'ent_is_sum') ) {
            Schema::table('check_list', function (Blueprint $table)
            {
                $table->dropColumn('ent_is_sum');
            });
        }
    }
}
