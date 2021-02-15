<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToLibraryCatalog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('library_literature_catalogs', function (Blueprint $table) {
            $table->string('publisher')->nullable();
            $table->string('publication_place')->nullable();
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
            $table->dropColumn('publisher');
            $table->dropColumn('publication_place');
        });
    }
}
