<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeFieldSizeMgApplication extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE mg_applications MODIFY nameeducation TEXT');
        DB::statement('ALTER TABLE mg_applications MODIFY numeducation TEXT');
        DB::statement('ALTER TABLE mg_applications MODIFY sereducation TEXT');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
