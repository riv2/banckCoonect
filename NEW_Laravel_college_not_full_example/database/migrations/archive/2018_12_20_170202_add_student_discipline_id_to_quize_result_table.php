<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStudentDisciplineIdToQuizeResultTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quize_result', function (Blueprint $table) {
            $table->integer('student_discipline_id')->nullable()->after('discipline_id');
            $table->foreign('student_discipline_id')->references('id')->on('students_disciplines')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quize_result', function (Blueprint $table) {
            $table->dropColumn('student_discipline_id');
        });
    }
}
