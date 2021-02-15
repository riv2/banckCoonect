<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLangsToSyllabusDocumentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('syllabus_document', function (Blueprint $table) {
            DB::statement("alter table `syllabus_document` modify column `lang` enum ('ru', 'en', 'kz', 'fr', 'ar', 'de')");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('syllabus_document', function (Blueprint $table) {
            DB::statement("alter table `syllabus_document` modify column `lang` enum ('ru', 'en', 'kz')");
        });
    }
}
