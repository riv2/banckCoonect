<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ReplaceQualificationFieldInSpecialitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('specialities', function (Blueprint $table) {
            $table->dropColumn('qualification_ru');
            $table->dropColumn('qualification_kz');
            $table->dropColumn('qualification_en');

            $table->integer('qualification_id')->nullable()->after('goals_en');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('specialities', function (Blueprint $table) {
            $table->text('qualification_ru')->nullable()->after('goals_en');
            $table->text('qualification_kz')->nullable()->after('qualification_ru');
            $table->text('qualification_en')->nullable()->after('qualification_kz');

            $table->dropColumn('qualification_id');
        });
    }
}
