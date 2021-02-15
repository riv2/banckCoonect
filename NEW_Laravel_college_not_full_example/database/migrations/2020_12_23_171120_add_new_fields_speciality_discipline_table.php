<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewFieldsSpecialityDisciplineTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('speciality_discipline', function (Blueprint $table) {
            $table->integer('verbal_sro')->nullable();
            $table->integer('sro_hours')->nullable();
            $table->integer('laboratory_hours')->nullable();
            $table->integer('practical_hours')->nullable();  
            $table->integer('lecture_hours')->nullable();
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
            $table->dropColumn('verbal_sro');
            $table->dropColumn('sro_hours');
            $table->dropColumn('laboratory_hours');
            $table->dropColumn('practical_hours');
            $table->dropColumn('lecture_hours');
        });
    }
}
