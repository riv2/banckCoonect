<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppealsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('appeals')) {
            Schema::create('appeals', function (Blueprint $table) {
                $table->increments('id');

                $table->unsignedInteger('user_id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

                $table->integer('student_discipline_id');
                $table->foreign('student_discipline_id')->references('id')->on('students_disciplines')->onDelete('cascade');

                $table->string('type', 5);
                $table->string('reason', 1000);
                $table->string('file', 100)->nullable();

                $table->unsignedInteger('expert1_id')->nullable();
                $table->string('expert1_resolution', 20)->nullable();
                $table->string('expert1_resolution_text', 1000)->nullable();
                $table->dateTime('expert1_resolution_date')->nullable();

                $table->unsignedInteger('expert2_id')->nullable();
                $table->string('expert2_resolution', 20)->nullable();
                $table->string('expert2_resolution_text', 1000)->nullable();
                $table->dateTime('expert2_resolution_date')->nullable();

                $table->unsignedInteger('expert3_id')->nullable();
                $table->string('expert3_resolution', 20)->nullable();
                $table->string('expert3_resolution_text', 1000)->nullable();
                $table->dateTime('expert3_resolution_date')->nullable();

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
        Schema::dropIfExists('appeals');
    }
}
