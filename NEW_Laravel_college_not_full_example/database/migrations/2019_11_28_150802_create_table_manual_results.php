<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableManualResults extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('manual_results')) {
            Schema::create('manual_results', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedInteger('teacher_id');
                $table->integer('discipline_id');
                $table->unsignedInteger('study_group_id');
                $table->unsignedInteger('student_id');
                $table->integer('student_discipline_id');

                $table->unsignedTinyInteger('sro_old')->nullable();
                $table->unsignedTinyInteger('sro_new')->nullable();
                $table->unsignedTinyInteger('exam_old')->nullable();
                $table->unsignedTinyInteger('exam_new')->nullable();

                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('manual_results');
    }
}
