<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSyllabusDocumentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('syllabus_document', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('syllabus_id');
            $table->foreign('syllabus_id')->references('id')->on('syllabus');

            $table->enum('resource_type', ['file', 'link']);
            $table->enum('material_type', ['teoretical', 'practical']);
            $table->string('filename')->nullable();
            $table->string('link')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('syllabus_document');
    }
}
