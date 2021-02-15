<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TrendsAddNewColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trends', function (Blueprint $table) {
            $table->string('classif_direction',255)->nullable(true);
            $table->string('classif_direction_kz',255)->nullable(true);
            $table->string('classif_direction_en',255)->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trends', function (Blueprint $table) {
            $table->dropColumn('classif_direction');
            $table->dropColumn('classif_direction_kz');
            $table->dropColumn('classif_direction_en');
        });
    }
}
