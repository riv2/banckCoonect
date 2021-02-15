<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CoursesTopicsAddNewColumnsNews extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('courses_topics', function (Blueprint $table) {
            $table->string('title',255)->nullable(false);
            $table->string('resource_link',255)->nullable(true);
            $table->string('resource_file',255)->nullable(true);
            $table->text('questions')->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('courses_topics', function (Blueprint $table) {
            $table->dropColumn('title');
            $table->dropColumn('resource_link');
            $table->dropColumn('resource_file');
            $table->dropColumn('questions');
        });
    }
}
