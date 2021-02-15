<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectSectionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_section', function (Blueprint $table) {
            $table->increments('id');

            $table->string('url');
            $table->string('name_ru')->nullable();
            $table->string('name_kz')->nullable();
            $table->string('name_en')->nullable();
            $table->enum('project', ['admin', 'student', 'teacher']);

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
        Schema::dropIfExists('project_section');
    }
}
