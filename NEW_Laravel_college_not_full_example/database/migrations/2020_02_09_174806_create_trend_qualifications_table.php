<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrendQualificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trend_qualifications', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('trend_id');
            $table->foreign('trend_id')->references('id')->on('trends')->onDelete('cascade');

            $table->string('name_ru');
            $table->string('name_kz');
            $table->string('name_en');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trend_qualifications');
    }
}
