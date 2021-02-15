<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddExamAndSroZeroedByRime extends Migration
{
    public function up()
    {
        Schema::table(
            'students_disciplines',
            function (Blueprint $table) {
                $table->boolean('exam_zeroed_by_time')->default(0)->after('test_blur');
                $table->boolean('sro_zeroed_by_time')->default(0)->after('task_blur');
            }
        );
    }

    public function down()
    {
        Schema::table(
            'students_disciplines',
            function (Blueprint $table) {
                $table->dropColumn('exam_zeroed_by_time');
                $table->dropColumn('sro_zeroed_by_time');
            }
        );
    }
}