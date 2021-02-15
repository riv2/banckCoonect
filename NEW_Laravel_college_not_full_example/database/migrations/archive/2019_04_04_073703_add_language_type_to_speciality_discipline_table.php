<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLanguageTypeToSpecialityDisciplineTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('speciality_discipline', function (Blueprint $table) {
            $table->enum('language_type', ['native', 'second', 'other'])
                ->nullable()->default('native')->after('discipline_id');
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
            $table->dropColumn('language_type');
        });
    }
}
