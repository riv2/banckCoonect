<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteLanguageLevelIdFromDisciplines extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {


        Schema::table('disciplines', function (Blueprint $table) {
            $table->dropForeign('disciplines_language_level_id_foreign');
            $table->dropColumn('language_level_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('disciplines', function (Blueprint $table) {
            $table->unsignedInteger('language_level_id')->nullable()->after('lang');
            $table->foreign('language_level_id')
                ->references('id')
                ->on('language_level')
                ->onDelete('cascade');
        });
    }
}
