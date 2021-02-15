<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeTextFiledsTypesOnsyllabus extends Migration
{
    public function __construct() {
        // Register ENUM type
        DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('syllabus', function (Blueprint $table) {
            $table->string('theme_number', 20)->nullable()->change();
            $table->string('theme_number_en', 20)->nullable()->change();
            $table->string('theme_number_kz', 20)->nullable()->change();
            $table->string('theme_number_fr', 20)->nullable()->change();
            $table->string('theme_number_ar', 20)->nullable()->change();
            $table->string('theme_number_de', 20)->nullable()->change();

            $table->string('theme_name', 2000)->nullable()->change();
            $table->string('theme_name_en', 2000)->nullable()->change();
            $table->string('theme_name_kz', 2000)->nullable()->change();
            $table->string('theme_name_fr', 2000)->nullable()->change();
            $table->string('theme_name_ar', 2000)->nullable()->change();
            $table->string('theme_name_de', 2000)->nullable()->change();

        });

        DB::statement('ALTER TABLE syllabus MODIFY literature TEXT;');
        DB::statement('ALTER TABLE syllabus MODIFY literature_en TEXT;');
        DB::statement('ALTER TABLE syllabus MODIFY literature_kz TEXT;');
        DB::statement('ALTER TABLE syllabus MODIFY literature_fr TEXT;');
        DB::statement('ALTER TABLE syllabus MODIFY literature_ar TEXT;');
        DB::statement('ALTER TABLE syllabus MODIFY literature_de TEXT;');

        DB::statement('ALTER TABLE syllabus MODIFY literature_added TEXT;');
        DB::statement('ALTER TABLE syllabus MODIFY teoretical_description LONGTEXT;');
        DB::statement('ALTER TABLE syllabus MODIFY practical_description TEXT;');
        DB::statement('ALTER TABLE syllabus MODIFY sro_description TEXT;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('syllabus', function (Blueprint $table) {
            $table->string('theme_number', 255)->nullable()->change();
            $table->string('theme_number_en', 255)->nullable()->change();
            $table->string('theme_number_kz', 255)->nullable()->change();
            $table->string('theme_number_fr', 255)->nullable()->change();
            $table->string('theme_number_ar', 255)->nullable()->change();
            $table->string('theme_number_de', 255)->nullable()->change();

            $table->mediumText('theme_name')->nullable()->change();
            $table->mediumText('theme_name_en')->nullable()->change();
            $table->mediumText('theme_name_kz')->nullable()->change();
            $table->mediumText('theme_name_fr')->nullable()->change();
            $table->mediumText('theme_name_ar')->nullable()->change();
            $table->mediumText('theme_name_de')->nullable()->change();

            DB::statement('ALTER TABLE `syllabus` CHANGE `literature` `literature` LONGTEXT NULL ;');
            DB::statement('ALTER TABLE `syllabus` CHANGE `literature_en` `literature_en` LONGTEXT NULL ;');
            DB::statement('ALTER TABLE `syllabus` CHANGE `literature_kz` `literature_kz` LONGTEXT NULL ;');
            DB::statement('ALTER TABLE `syllabus` CHANGE `literature_fr` `literature_fr` LONGTEXT NULL ;');
            DB::statement('ALTER TABLE `syllabus` CHANGE `literature_ar` `literature_ar` LONGTEXT NULL ;');
            DB::statement('ALTER TABLE `syllabus` CHANGE `literature_de` `literature_de` LONGTEXT NULL ;');

            DB::statement('ALTER TABLE `syllabus` CHANGE `literature_added` `literature_added` LONGTEXT NULL ;');
            DB::statement('ALTER TABLE `syllabus` CHANGE `teoretical_description` `teoretical_description` LONGTEXT NULL ;');
            DB::statement('ALTER TABLE `syllabus` CHANGE `practical_description` `practical_description` LONGTEXT NULL ;');
            DB::statement('ALTER TABLE `syllabus` CHANGE `sro_description` `sro_description` LONGTEXT NULL ;');
        });
    }
}
