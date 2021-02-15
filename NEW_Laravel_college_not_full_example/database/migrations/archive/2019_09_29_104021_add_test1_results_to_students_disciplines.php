<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTest1ResultsToStudentsDisciplines extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('students_disciplines', function (Blueprint $table) {
            $table->unsignedTinyInteger('test1_result')->after('discipline_id')->nullable()->comment('Test 1 result in percents');
            $table->unsignedTinyInteger('test1_result_points')->after('test1_result')->nullable()->comment('Test 1 result in points');

//            DB::statement('ALTER TABLE students_disciplines CHANGE test_result test_result TINYINT(3) UNSIGNED NULL DEFAULT NULL;');
//            $table->unsignedTinyInteger('test_result')->nullable()->comment('Summary test result in percents')->change();
            $table->string('test_result_letter', 2)->nullable()->change();
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
            $table->dropColumn('test1_result');
            $table->dropColumn('test1_result_points');

            $table->integer('test_result')->nullable()->comment('')->change();
            $table->string('test_result_letter', 255)->nullable()->change();
        });
    }
}
