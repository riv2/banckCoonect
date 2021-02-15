<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class DeleteLanguagesFromSyllabus extends Migration
{
    public function up()
    {
        Schema::table(
            'syllabus',
            function (Blueprint $table) {
                $table->dropColumn('theme_number_en');
                $table->dropColumn('theme_number_kz');
                $table->dropColumn('theme_number_fr');
                $table->dropColumn('theme_number_ar');
                $table->dropColumn('theme_number_de');

                $table->dropColumn('theme_name_en');
                $table->dropColumn('theme_name_kz');
                $table->dropColumn('theme_name_fr');
                $table->dropColumn('theme_name_ar');
                $table->dropColumn('theme_name_de');

                $table->dropColumn('literature_en');
                $table->dropColumn('literature_kz');
                $table->dropColumn('literature_fr');
                $table->dropColumn('literature_ar');
                $table->dropColumn('literature_de');
            }
        );
    }

    public function down()
    {
        Schema::table(
            'syllabus',
            function (Blueprint $table) {
                $table->string('theme_number_en', 20)->after('theme_number')->nullable();
                $table->string('theme_number_kz', 20)->after('theme_number_en')->nullable();
                $table->string('theme_number_fr', 20)->after('theme_number_kz')->nullable();
                $table->string('theme_number_ar', 20)->after('theme_number_fr')->nullable();
                $table->string('theme_number_de', 20)->after('theme_number_ar')->nullable();

                $table->string('theme_name_en', 2000)->after('theme_name')->nullable();
                $table->string('theme_name_kz', 2000)->after('theme_name_en')->nullable();
                $table->string('theme_name_fr', 2000)->after('theme_name_kz')->nullable();
                $table->string('theme_name_ar', 2000)->after('theme_name_fr')->nullable();
                $table->string('theme_name_de', 2000)->after('theme_name_ar')->nullable();

                $table->text('literature_en')->after('literature')->nullable();
                $table->text('literature_kz')->after('literature_en')->nullable();
                $table->text('literature_fr')->after('literature_kz')->nullable();
                $table->text('literature_ar')->after('literature_fr')->nullable();
                $table->text('literature_de')->after('literature_ar')->nullable();
            }
        );
    }
}