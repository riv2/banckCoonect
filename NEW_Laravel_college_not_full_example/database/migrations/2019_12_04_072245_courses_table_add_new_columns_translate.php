<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CoursesTableAddNewColumnsTranslate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->string('title_kz',255)->nullable(true);
            $table->string('title_en',255)->nullable(true);
            $table->text('title_card_kz')->nullable(true);
            $table->text('title_card_en')->nullable(true);
            $table->text('description_kz')->nullable(true);
            $table->text('description_en')->nullable(true);
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
            $table->dropColumn('title_kz');
            $table->dropColumn('title_en');
            $table->dropColumn('title_card_kz');
            $table->dropColumn('title_card_en');
            $table->dropColumn('description_kz');
            $table->dropColumn('description_en');
        });
    }
}
