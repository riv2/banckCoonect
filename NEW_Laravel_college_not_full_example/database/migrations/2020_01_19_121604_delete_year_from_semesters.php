<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class DeleteYearFromSemesters extends Migration
{
    public function up()
    {
        Schema::table(
            'semesters',
            function (Blueprint $table) {
                $table->dropColumn('year');
            }
        );
    }

    public function down()
    {
        Schema::table(
            'semesters',
            function (Blueprint $table) {
                $table->integer('year')->after('number')->nullable();
            }
        );
    }
}