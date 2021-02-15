<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSyllabusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('syllabus', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('discipline_id');
            $table->foreign('discipline_id')->references('id')->on('disciplines');

            $table->string('theme_number');
            $table->string('theme_name');
            $table->text('literature');
            $table->float('contact_hours');
            $table->float('self_hours');
            $table->float('self_with_teacher_hours');

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
        Schema::dropIfExists('syllabus');
    }
}
