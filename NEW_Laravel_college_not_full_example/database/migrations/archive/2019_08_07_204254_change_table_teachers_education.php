<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeTableTeachersEducation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('teachers_education', function (Blueprint $table) {
            $table->dropColumn('type');
        });
        Schema::table('teachers_education', function (Blueprint $table) {
            $table->enum('type', [
                'bachelor',
                'specialist',
                'magistracy',
                'scientific_degree',
                'academic_status',
                'language_ability',
                'additional_skill'
            ])->default('bachelor');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
