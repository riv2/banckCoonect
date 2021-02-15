<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('courses');

        Schema::create('courses', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('user_id');
            $table->string('title')->nullable();
            $table->string('photo_file_name')->nullable();
            $table->text('description');
            $table->string('language');
            $table->string('certificate_file_name')->nullable();
            $table->text('video_link')->nullable();
            $table->text('tags');
            $table->enum('status', ['moderation', 'active']);

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
        Schema::dropIfExists('courses');
    }
}
