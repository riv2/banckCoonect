<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SyllabusTaskCreateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('syllabus_task')) {
            Schema::create('syllabus_task', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('syllabus_id');
                $table->enum('type', [
                    'text',
                    'img',
                    'link',
                    'audio',
                    'video',
                    'event'
                ])->default('text');
                $table->integer('points')->default(0);
                $table->datetime('event_date')->nullable(true);
                $table->text('event_place')->nullable(true);

                $table->timestamps();
                $table->softDeletes();

                $table->foreign('syllabus_id')->references('id')->on('syllabus')->onDelete('cascade');

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
        Schema::dropIfExists('syllabus_task');
    }
}
