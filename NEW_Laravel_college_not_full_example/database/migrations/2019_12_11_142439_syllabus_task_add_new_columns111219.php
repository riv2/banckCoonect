<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SyllabusTaskAddNewColumns111219 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('syllabus_task', function (Blueprint $table) {
            $table->integer('discipline_id')->after('syllabus_id');
            $table->enum('language', ['kz', 'ru', 'en'])->default('ru')->after('discipline_id');
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
            $table->dropColumn('discipline_id');
            $table->dropColumn('language');
        });
    }
}
