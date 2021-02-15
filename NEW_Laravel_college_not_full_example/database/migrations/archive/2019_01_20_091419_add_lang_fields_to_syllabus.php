<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLangFieldsToSyllabus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('syllabus', function (Blueprint $table) {
            $table->string('theme_number_en')->nullable()->after('theme_number');
            $table->string('theme_number_kz')->nullable()->after('theme_number_en');
            $table->string('theme_name_en')->nullable()->after('theme_name');
            $table->string('theme_name_kz')->nullable()->after('theme_name_en');
            $table->string('literature_en')->nullable()->after('literature');
            $table->string('literature_kz')->nullable()->after('literature_en');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('syllabus', function (Blueprint $table) {
            $table->dropColumn('theme_number_en');
            $table->dropColumn('theme_number_kz');
            $table->dropColumn('theme_name_en');
            $table->dropColumn('theme_name_kz');
            $table->dropColumn('literature_en');
            $table->dropColumn('literature_kz');
        });
    }
}
