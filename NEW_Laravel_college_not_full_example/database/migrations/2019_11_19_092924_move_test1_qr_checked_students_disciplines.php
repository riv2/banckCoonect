<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MoveTest1QrCheckedStudentsDisciplines extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('students_disciplines', function (Blueprint $table) {
            $table->boolean('test1_qr_checked1')->nullable()->after('test1_blur');
        });

        DB::statement('UPDATE `students_disciplines` SET `test1_qr_checked1` = `test1_qr_checked`');

        Schema::table('students_disciplines', function (Blueprint $table) {
            $table->dropColumn('test1_qr_checked');
            $table->renameColumn('test1_qr_checked1', 'test1_qr_checked');
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
            $table->boolean('test1_qr_checked1')->nullable()->after('is_elective');
        });

        DB::statement('UPDATE `students_disciplines` SET `test1_qr_checked1` = `test1_qr_checked`');

        Schema::table('students_disciplines', function (Blueprint $table) {
            $table->dropColumn('test1_qr_checked');
            $table->renameColumn('test1_qr_checked1', 'test1_qr_checked');
        });
    }
}
