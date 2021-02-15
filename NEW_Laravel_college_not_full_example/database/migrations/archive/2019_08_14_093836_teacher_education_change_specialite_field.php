<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TeacherEducationChangeSpecialiteField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('teachers_education', function (Blueprint $table) {
            $table->dropForeign('teachers_education_speciality_id_foreign');
            $table->dropColumn('speciality_id');
        });
        Schema::table('teachers_education', function (Blueprint $table) {
            $table->string('speciality', 255)->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('teachers_education', function (Blueprint $table) {
            $table->dropColumn('speciality');
        });
        Schema::table('teachers_education', function (Blueprint $table) {
            $table->unsignedInteger('speciality_id')->nullable(true);
            $table->foreign('speciality_id')->references('id')->on('specialities');
        });


    }
}
