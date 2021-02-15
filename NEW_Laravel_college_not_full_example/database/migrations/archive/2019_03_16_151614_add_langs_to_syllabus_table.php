<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLangsToSyllabusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('syllabus', function (Blueprint $table) {
            $table->string('theme_number_fr')->after('theme_number_kz');
            $table->string('theme_number_ar')->after('theme_number_fr');
            $table->string('theme_number_de')->after('theme_number_ar');

            $table->string('theme_name_fr')->after('theme_name_kz');
            $table->string('theme_name_ar')->after('theme_name_fr');
            $table->string('theme_name_de')->after('theme_name_ar');

            $table->longText('literature_fr')->after('literature_kz');
            $table->longText('literature_ar')->after('literature_fr');
            $table->longText('literature_de')->after('literature_ar');
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
            $table->dropColumn('theme_number_fr');
            $table->dropColumn('theme_number_ar');
            $table->dropColumn('theme_number_de');

            $table->dropColumn('theme_name_fr');
            $table->dropColumn('theme_name_ar');
            $table->dropColumn('theme_name_de');

            $table->dropColumn('literature_fr');
            $table->dropColumn('literature_ar');
            $table->dropColumn('literature_de');
        });
    }
}
