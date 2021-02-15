<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddIdsToStudentFinanceNomenclature extends Migration
{
    public function up()
    {
        Schema::table(
            'student_finance_nomenclature',
            function (Blueprint $table) {
                $table->integer('student_discipline_id')->nullable()->after('finance_nomenclature_id');
                $table->string('comment', 100)->nullable()->after('student_discipline_id');
            }
        );
    }

    public function down()
    {
        Schema::table(
            'student_finance_nomenclature',
            function (Blueprint $table) {
                $table->dropColumn('student_discipline_id');
                $table->dropColumn('comment');
            }
        );
    }
}