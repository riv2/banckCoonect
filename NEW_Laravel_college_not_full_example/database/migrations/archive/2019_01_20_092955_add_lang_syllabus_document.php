<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLangSyllabusDocument extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('syllabus_document', function (Blueprint $table) {
            $table->enum('lang', ['ru', 'en', 'kz'])->nullable()->after('syllabus_id');
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
            $table->dropColumn('lang');
        });
    }
}
