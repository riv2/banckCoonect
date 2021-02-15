<?php

use App\FinanceNomenclature;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSemesterToStudentFinanceNomenclature extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('student_finance_nomenclature', function (Blueprint $table) {
            $table->unsignedTinyInteger('semester')->nullable()->after('cost');
        });

        Schema::table('finance_nomenclatures', function (Blueprint $table) {
            $table->boolean('only_one_per_semester')->default(0)->comment('Разрешена только одна покупка в семестр');
        });

        FinanceNomenclature::where('id', FinanceNomenclature::TRANSIT_CLASS_ATTENDANCE_ID)->update(['only_one' => 0, 'only_one_per_semester' => 1]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('student_finance_nomenclature', function (Blueprint $table) {
            $table->dropColumn('semester');
        });

        Schema::table('finance_nomenclatures', function (Blueprint $table) {
            $table->dropColumn('only_one_per_semester');
        });
    }
}
