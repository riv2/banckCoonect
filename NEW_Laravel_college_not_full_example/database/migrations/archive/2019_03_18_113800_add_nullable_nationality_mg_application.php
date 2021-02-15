<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNullableNationalityMgApplication extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE mg_applications MODIFY nationality_id INT NULL');
        /*Schema::table('mg_applications', function (Blueprint $table) {
            $table->integer('nationality_id')->nullable(true)->change();
        });*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE mg_applications MODIFY nationality_id INT NOT NULL');
        /*Schema::table('mg_applications', function (Blueprint $table) {
            $table->integer('nationality_id')->nullable(false)->change();
        });*/
    }
}
