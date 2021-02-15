<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInfoNewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('info_news', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('user_id')->comment('Author');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->string('title_ru');
            $table->string('title_kz');
            $table->string('title_en');

            $table->text('text_preview_ru');
            $table->text('text_preview_kz');
            $table->text('text_preview_en');

            $table->mediumText('text_ru');
            $table->mediumText('text_kz');
            $table->mediumText('text_en');

            $table->boolean('is_important');
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
        Schema::dropIfExists('info_news');
    }
}
