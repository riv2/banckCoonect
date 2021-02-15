<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveHasDiplomworkColumnFromSpecialityDiscipline extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('speciality_discipline', 'has_diplomwork')) {
            Schema::table('speciality_discipline', function (Blueprint $table)
            {
                $table->dropColumn('has_diplomwork');
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
        //
    }
}
