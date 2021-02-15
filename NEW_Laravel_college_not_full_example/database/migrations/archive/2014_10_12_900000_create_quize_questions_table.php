<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuizeQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('quize_questions')) {
            Schema::create('quize_questions', function (Blueprint $table) {
                $table->integer('id', true);

                $table->integer('discipline_id')->nullable();
                $table->longText('question')->nullable();
                $table->integer('teacher_id')->nullable();
                $table->integer('total_points')->nullable();

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
        Schema::dropIfExists('quize_questions');
    }
}
