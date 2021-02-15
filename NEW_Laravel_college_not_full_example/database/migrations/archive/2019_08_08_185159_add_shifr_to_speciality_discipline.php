<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddShifrToSpecialityDiscipline extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('speciality_discipline', function (Blueprint $table) {
            $table->string('pressmark')->after('exam')->nullable()->comment('Шифр');
            $table->integer('semester')->after('pressmark')->nullable();
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
            $table->dropColumn('pressmark');
            $table->dropColumn('semester');
        });
    }
}
