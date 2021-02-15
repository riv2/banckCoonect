<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SpecialityDisciplineAddNewColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('speciality_discipline', function (Blueprint $table) {
            $table->string('discipline_cicle',255)->nullable(true);
            $table->string('mt_tk',255)->nullable(true);
            $table->tinyInteger('has_coursework')->default(0);
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
            $table->dropColumn('discipline_cicle');
            $table->dropColumn('mt_tk');
            $table->dropColumn('has_coursework');
        });
    }
}
