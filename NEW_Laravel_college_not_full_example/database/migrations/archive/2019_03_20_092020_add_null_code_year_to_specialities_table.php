<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNullCodeYearToSpecialitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE specialities MODIFY code varchar(255) NULL');
        DB::statement('ALTER TABLE specialities MODIFY year varchar(255) NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE specialities MODIFY code varchar(255) NOT NULL');
        DB::statement('ALTER TABLE specialities MODIFY year varchar(255) NOT NULL');
    }
}
