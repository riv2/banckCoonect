<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeForStudentsVisitsGeneratorTableStudentCheckin extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('student_checkin', function (Blueprint $table) {
            $table->unsignedInteger('teacher_id')->nullable()->change();
            $table->tinyInteger('is_generated')->default(0)->after('teacher_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('student_checkin', function (Blueprint $table)
        {
            $table->unsignedInteger('teacher_id')->nullable(false)->change();
        });

        if (Schema::hasColumn('student_checkin', 'is_generated')) {
            Schema::table('student_checkin', function (Blueprint $table)
            {
                $table->dropColumn('is_generated');
            });
        }
    }
}
