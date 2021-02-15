<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPayProcessingToStudentsDisciplines extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if (!Schema::hasColumn('students_disciplines', 'pay_processing'))
        {
            Schema::table('students_disciplines', function (Blueprint $table) {
                $table->boolean('pay_processing')->after('student_id')->nullable()->comment('Выполнется запрос к 1C');
            });
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('students_disciplines', function (Blueprint $table) {
            $table->dropColumn('pay_processing');
        });
    }
}
