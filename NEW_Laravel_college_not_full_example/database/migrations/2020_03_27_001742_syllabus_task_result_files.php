<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SyllabusTaskResultFiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('syllabus_task_result_files')) {
            Schema::create('syllabus_task_result_files', function (Blueprint $table) {

                $table->increments('id');

                $table->integer('user_id')->nullable(false);
                $table->integer('discipline_id')->nullable(false);
                $table->integer('syllabus_id')->nullable(false);
                $table->integer('task_id')->nullable(false);
                $table->string('name')->nullable(false);
                $table->string('filename')->nullable(false);

                $table->timestamps();
                $table->softDeletes();

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
        Schema::dropIfExists('syllabus_task_result_files');
    }
}
