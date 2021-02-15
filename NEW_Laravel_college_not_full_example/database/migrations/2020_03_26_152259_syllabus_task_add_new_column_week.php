<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SyllabusTaskAddNewColumnWeek extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if ( !Schema::hasColumn('syllabus_task', 'week') ) {
            Schema::table('syllabus_task', function (Blueprint $table)
            {
                $table->integer('week')->nullable(true)->after('points');
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
        if ( Schema::hasColumn('syllabus_task', 'week') ) {
            Schema::table('syllabus_task', function (Blueprint $table)
            {
                $table->dropColumn('week');
            });
        }
    }
}
