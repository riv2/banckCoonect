<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLibratyCatalogKnowledgeSectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('libraty_catalog_knowledge_sections', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('literature_catalog_id');
            $table->integer('knowledge_section_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('libraty_catalog_knowledge_sections');
    }
}
