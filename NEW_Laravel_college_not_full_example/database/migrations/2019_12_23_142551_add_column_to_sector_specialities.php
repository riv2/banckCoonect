<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToSectorSpecialities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sector_specialities', function (Blueprint $table) {
            $table->dropColumn('speciality_id');
            $table->integer('speciality_bc_id')->nullable();
            $table->integer('speciality_mg_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sector_specialities', function (Blueprint $table) {
            $table->integer('speciality_id');
            $table->dropColumn('speciality_bc_id');
            $table->dropColumn('speciality_mg_id');
        });
    }
}
