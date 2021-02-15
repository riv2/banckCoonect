<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SyllabusDocumentMaterialTypeAddNewTypeSrop extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE syllabus_document MODIFY COLUMN material_type ENUM('teoretical','practical','sro','srop')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE syllabus_document MODIFY COLUMN material_type ENUM('teoretical','practical','sro')");
    }
}
