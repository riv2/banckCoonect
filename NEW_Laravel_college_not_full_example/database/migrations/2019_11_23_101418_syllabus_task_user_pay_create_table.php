<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SyllabusTaskUserPayCreateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('syllabus_task_user_pay')) {
            Schema::create('syllabus_task_user_pay', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('task_id');
                $table->unsignedInteger('user_id');
                $table->tinyInteger('active')->default(1);
                $table->tinyInteger('payed')->nullable(true);
                $table->timestamps();

                $table->foreign('task_id')->references('id')->on('syllabus_task')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('syllabus_task_user_pay');
    }
}
