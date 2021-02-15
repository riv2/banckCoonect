<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class createDisciplinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('disciplines')) {
            Schema::create('disciplines', function (Blueprint $table) {
                $table->integer('id', true);

                $table->string('name', 260)->nullable();
                $table->integer('credits')->nullable();
                $table->integer('kz')->nullable();
                $table->integer('ru')->nullable();
                $table->integer('en')->nullable();
                $table->string('syllabus')->nullable();

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
        Schema::dropIfExists('disciplines');
    }
}
