<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddBoolManualResultsToStudentsDisciplines extends Migration
{
    public function up()
    {
        Schema::table(
            'students_disciplines',
            function (Blueprint $table) {
                $table->boolean('test_manual')->after('test_date')->default(0);
                $table->boolean('final_manual')->after('final_date')->default(0);
                $table->boolean('task_manual')->after('task_date')->default(0);
            }
        );
    }

    public function down()
    {
        Schema::table(
            'students_disciplines',
            function (Blueprint $table) {
                $table->dropColumn('test_manual');
                $table->dropColumn('final_manual');
                $table->dropColumn('task_manual');
            }
        );
    }
}