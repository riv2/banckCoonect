<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSemesterSpecialityDisciplineTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('speciality_discipline', function (Blueprint $table) {
            if (Schema::hasColumn('speciality_discipline', 'semester'))
            {
                $table->dropColumn('semester');
            }
            $table->integer('semester')->references('id')->on('semesters')->onDelete('cascade')->nullable();
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
            $table->dropColumn('semester');
        });
    }
}
