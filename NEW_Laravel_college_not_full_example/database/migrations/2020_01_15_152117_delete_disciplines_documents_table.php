<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteDisciplinesDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::drop('disciplines_documents');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('disciplines_documents', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('disciplines_id');
            $table->string('file_name');
            $table->string('file_name_original');
            $table->string('document_type');
            $table->timestamps();
        });
    }
}
