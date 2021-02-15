<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDisciplineFullAccordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discipline_full_accord', function (Blueprint $table) {
            $table->integer('discipline_id');
            $table->integer('remote_discipline_id');

            $table->primary(['discipline_id', 'remote_discipline_id'], 'discipline_full_accord_id_remote_id');
            $table->foreign('discipline_id')->references('id')->on('disciplines');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('discipline_full_accord');
    }
}
