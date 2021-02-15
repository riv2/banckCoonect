<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDescLangFieldsToSpecialities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('specialities', function (Blueprint $table) {
            $table->mediumText('description_kz')->nullable()->after('description');
            $table->mediumText('description_en')->nullable()->after('description_kz');
            $table->mediumText('goals_kz')->nullable()->after('goals');
            $table->mediumText('goals_en')->nullable()->after('goals_kz');
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
            $table->dropColumn('description_kz');
            $table->dropColumn('description_en');
            $table->dropColumn('goals_kz');
            $table->dropColumn('goals_en');
        });
    }
}
