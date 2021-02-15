<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CoursesTableChangeColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->integer('user_id')->nullable(true);
            $table->string('user_name',255)->nullable(true);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('user_id');
            $table->dropColumn('user_name');
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->integer('user_id')->nullable(false);
        });
    }
}
