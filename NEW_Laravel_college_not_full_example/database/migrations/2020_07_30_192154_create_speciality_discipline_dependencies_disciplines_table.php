<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpecialityDisciplineDependenciesDisciplinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('speciality_discipline_dependencies_disciplines', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('speciality_discipline_dependence_id');
            $table->integer('discipline_id');
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
        Schema::dropIfExists('speciality_discipline_dependencies_disciplines');
    }
}
