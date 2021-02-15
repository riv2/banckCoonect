<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExamTypeToSpecialityDisciplines extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('speciality_discipline', function (Blueprint $table) {
            $table->enum('exam_type', ['written', 'oral', 'test', 'complex'])
                ->nullable()->after('language_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('speciality_discipline', function (Blueprint $table) {
            $table->dropColumn('exam_type');
        });
    }
}
