<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SetNullablesToMgApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mg_applications', function (Blueprint $table) {
            DB::statement('ALTER TABLE mg_applications MODIFY citizenship_id integer NULL');
            DB::statement('ALTER TABLE mg_applications MODIFY region_id integer NULL');
            DB::statement('ALTER TABLE mg_applications MODIFY city_id integer NULL');
            DB::statement('ALTER TABLE mg_applications MODIFY street varchar(255) NULL');
            DB::statement('ALTER TABLE mg_applications MODIFY building_number varchar(255) NULL');
            DB::statement('ALTER TABLE mg_applications MODIFY kzornot TINYINT NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mg_applications', function (Blueprint $table) {

        });
    }
}
