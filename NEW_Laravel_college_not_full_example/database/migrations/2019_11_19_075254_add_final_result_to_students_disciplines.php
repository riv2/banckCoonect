<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFinalResultToStudentsDisciplines extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('students_disciplines', function (Blueprint $table) {
            $table->unsignedTinyInteger('final_result')->after('test_qr_checked')->nullable();
            $table->double('final_result_points')->after('final_result')->nullable();
            $table->double('final_result_gpi')->after('final_result_points')->nullable();
            $table->string('final_result_letter', 2)->after('final_result_gpi')->nullable();
        });

        DB::statement('UPDATE `students_disciplines` SET `final_result` = `test_result`, `final_result_points` = `test_result_points`, `final_result_gpi` = `test_result_gpi`, `final_result_letter` = `test_result_letter`');
        DB::statement('UPDATE `students_disciplines` SET `test_result` = NULL, `test_result_points` = NULL, `test_result_gpi` = NULL, `test_result_letter` = NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('UPDATE `students_disciplines` SET `test_result` = `final_result`, `test_result_points` = `final_result_points`, `test_result_gpi` = `final_result_gpi`, `test_result_letter` = `final_result_letter`');

        Schema::table('students_disciplines', function (Blueprint $table) {
            $table->dropColumn('final_result');
            $table->dropColumn('final_result_points');
            $table->dropColumn('final_result_gpi');
            $table->dropColumn('final_result_letter');
        });
    }
}
