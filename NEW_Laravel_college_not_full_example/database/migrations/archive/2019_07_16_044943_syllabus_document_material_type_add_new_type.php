<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SyllabusDocumentMaterialTypeAddNewType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE syllabus_document MODIFY COLUMN material_type ENUM('teoretical','practical','sro')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE syllabus_document MODIFY COLUMN material_type ENUM('teoretical','practical')");
    }
}
