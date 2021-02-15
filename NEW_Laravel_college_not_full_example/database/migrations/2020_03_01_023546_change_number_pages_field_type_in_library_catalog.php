<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeNumberPagesFieldTypeInLibraryCatalog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('library_literature_catalogs', function (Blueprint $table) {
            $table->string('number_pages')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('library_literature_catalogs', function (Blueprint $table) {
            $table->integer('number_pages')->change();
        });
    }
}
