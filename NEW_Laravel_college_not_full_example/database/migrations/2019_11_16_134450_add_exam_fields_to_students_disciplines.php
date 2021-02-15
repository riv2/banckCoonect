<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExamFieldsToStudentsDisciplines extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('students_disciplines', function (Blueprint $table) {
            $table->dateTime('test_date')->after('test_result_letter')->nullable();
            $table->boolean('test_result_trial')->after('test_date')->default(0);
            $table->boolean('test_blur')->after('test_result_trial')->default(0);
            $table->boolean('test_qr_checked')->after('test_blur')->default(0);
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
            $table->dropColumn('test_date');
            $table->dropColumn('test_result_trial');
            $table->dropColumn('test_blur');
            $table->dropColumn('test_qr_checked');
        });
    }
}
