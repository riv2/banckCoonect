<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuizeAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('quize_answers')) {
            Schema::create('quize_answers', function (Blueprint $table) {
                $table->integer('id', true);

                $table->integer('question_id')->nullable();
                $table->longText('answer')->nullable();
                $table->integer('points')->nullable();
                $table->string('img', 128)->nullable();

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
        Schema::dropIfExists('quize_answers');
    }
}
