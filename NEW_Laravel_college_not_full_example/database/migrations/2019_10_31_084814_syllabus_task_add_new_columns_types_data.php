<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SyllabusTaskAddNewColumnsTypesData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('syllabus_task', function (Blueprint $table) {
            $table->text('text_data')->nullable(true);
            $table->string('img_data',255)->nullable(true);
            $table->string('link_data',255)->nullable(true);
            $table->string('audio_data',255)->nullable(true);
            $table->string('video_data',255)->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('syllabus_task', function (Blueprint $table) {
            $table->dropColumn('text_data');
            $table->dropColumn('img_data');
            $table->dropColumn('link_data');
            $table->dropColumn('audio_data');
            $table->dropColumn('video_data');
        });
    }
}
