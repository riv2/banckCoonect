<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewFieldsToSpecialities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('specialities', function (Blueprint $table) {
            $table->mediumText('description')->nullable()->after('passing_ent_total');
            $table->mediumText('goals')->nullable()->after('description');
            $table->text('qualification_ru')->nullable()->after('goals');
            $table->text('qualification_kz')->nullable()->after('qualification_ru');
            $table->text('qualification_en')->nullable()->after('qualification_kz');
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
            $table->dropColumn('description');
            $table->dropColumn('goals');
            $table->dropColumn('qualification_ru');
            $table->dropColumn('qualification_kz');
            $table->dropColumn('qualification_en');
        });
    }
}
