<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditSyylabusFieldDefault extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('syllabus', function (Blueprint $table) {
            $table->string('theme_number')->nullable()->change();
            $table->string('theme_name')->nullable()->change();
            $table->string('literature')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('syllabus', function (Blueprint $table) {
            $table->string('theme_number')->change();
            $table->string('theme_name')->change();
            $table->string('literature')->change();
        });
    }
}
