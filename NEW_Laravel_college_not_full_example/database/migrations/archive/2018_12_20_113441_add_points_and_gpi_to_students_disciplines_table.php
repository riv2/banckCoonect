<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPointsAndGpiToStudentsDisciplinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('students_disciplines', function (Blueprint $table) {
            $table->float('test_result_points')->after('test_result')->nullable()->comment('Оценка в баллах');
            $table->float('test_result_gpi')->after('test_result_points')->nullable()->comment('Оценка gpi');
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
            $table->dropColumn('test_result_points');
            $table->dropColumn('test_result_gpi');
        });
    }
}
