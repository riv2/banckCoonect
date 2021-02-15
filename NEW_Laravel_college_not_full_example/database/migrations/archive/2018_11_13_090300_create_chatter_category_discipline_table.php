<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChatterCategoryDisciplineTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chatter_category_discipline', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->unsignedInteger('chatter_category_id');
            $table->foreign('chatter_category_id')->references('id')->on('chatter_categories');

            $table->integer('discipline_id');
            $table->foreign('discipline_id')->references('id')->on('disciplines');

            $table->timestamps();

            $table->primary(['chatter_category_id', 'discipline_id'], 'chatter_discipline_primary');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chatter_category_discipline');
    }
}
