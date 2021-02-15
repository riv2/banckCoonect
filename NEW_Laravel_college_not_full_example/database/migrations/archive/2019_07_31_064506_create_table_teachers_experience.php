<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTeachersExperience extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teachers_experience', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->dateTime('date_from');
            $table->dateTime('date_to');
            $table->text('workplace');
            $table->enum('type_experience', [
                'practical',
                'teaching',
                'other',
            ])->default('practical');
            $table->enum('current_experience', [
                'yes',
                'no'
            ])->default('no');
            $table->string('workstatus',255);
            $table->text('charges');
            $table->text('contacts');
            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('teachers_experience');
    }
}
