<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTest1LetterAndTest1TrialToStudentsDisciplines extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('students_disciplines', function (Blueprint $table) {
            $table->string('test1_result_letter', 2)->after('test1_result_points')->nullable();
            $table->boolean('test1_result_trial')->after('test1_result_letter')->nullable()->comment('Test 1 result is trial');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('students_disciplines', function (Blueprint $table) {
            $table->dropColumn('test1_result_letter');
            $table->dropColumn('test1_result_trial');
        });
    }
}
