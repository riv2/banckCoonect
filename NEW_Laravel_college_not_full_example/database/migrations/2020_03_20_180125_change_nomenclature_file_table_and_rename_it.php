<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeNomenclatureFileTableAndRenameIt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('nomenclature_folder_files', function (Blueprint $table) {
            $table->dropColumn('file');
            $table->boolean('isset_files')->default(0);
        });
        Schema::rename('nomenclature_folder_files', 'nomenclature_folder_templates');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('nomenclature_folder_templates', 'nomenclature_folder_files');
        Schema::table('nomenclature_folder_files', function (Blueprint $table) {
            $table->dropColumn('isset_files');
            $table->string('file')->nullable();
        });
    }
}
