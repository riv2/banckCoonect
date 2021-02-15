<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProfileTeachersChangeColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('profile_teachers', function (Blueprint $table) {
            $table->dropColumn('actual_address');
            $table->dropColumn('home_address');

            $table->integer('country_id')->nullable();
            $table->integer('region_id')->nullable();
            $table->integer('city_id')->nullable();
            $table->string('street')->nullable();
            $table->string('building_number', 32)->nullable();
            $table->string('apartment_number', 32)->nullable();
            $table->integer('home_country_id')->nullable();
            $table->integer('home_region_id')->nullable();
            $table->integer('home_city_id')->nullable();
            $table->string('home_street')->nullable();
            $table->string('home_building_number', 32)->nullable();
            $table->string('home_apartment_number', 32)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('profile_teachers', function (Blueprint $table) {
            $table->dropColumn('country_id');
            $table->dropColumn('region_id');
            $table->dropColumn('city_id');
            $table->dropColumn('street');
            $table->dropColumn('building_number');
            $table->dropColumn('apartment_number');
            $table->dropColumn('home_country_id');
            $table->dropColumn('home_region_id');
            $table->dropColumn('home_city_id');
            $table->dropColumn('home_street');
            $table->dropColumn('home_building_number');
            $table->dropColumn('home_apartment_number');

            $table->text('actual_address')->nullable(true);
            $table->text('home_address')->nullable(true);
        });
    }
}
