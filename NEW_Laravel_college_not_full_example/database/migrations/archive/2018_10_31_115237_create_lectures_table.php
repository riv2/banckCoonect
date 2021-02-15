<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLecturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lectures', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->unsignedInteger('course_id');
            $table->foreign('course_id')->references('id')->on('courses');

            $table->string('title');
            $table->text('description')->nullable();
            $table->smallInteger('count_minutes')->default(0)->comment('Продолжительность в минутах');
            $table->dateTime('start')->comment('Дата и время начала');
            $table->enum('type', ['online', 'offline']);
            $table->string('url')->nullable()->comment('Ссылка на ресурс вещания');
            $table->float('cost')->default(0);
            $table->float('rating')->default(0);
            $table->integer('room_booking_id')->nullable();

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
        Schema::dropIfExists('lectures');
    }
}
