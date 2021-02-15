<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMgApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mg_applications', function (Blueprint $table) {
            
            $table->increments('id');

            $table->integer('nationality_id');
            $table->integer('citizenship_id');
            $table->integer('family_status');
            $table->integer('region_id');
            $table->integer('city_id');
            
            $table->string('street');
            $table->string('building_number');
            $table->string('apartment_number');

            $table->string('residence_registration_photo');
            $table->string('military_photo');
            $table->string('r086_photo');
            $table->string('r063_photo');
/*
            $table->string('education');

            $table->string('bceducation');
            $table->integer('user_id');
            $table->string('numeducation');
            $table->string('sereducation');
            $table->string('nameeducation');
            $table->string('dateeducation');
            $table->string('cityeducation');
            $table->string('atteducation_photo');            
            $table->integer('kzornot');
            $table->string('eduspecialty');
            $table->string('typevocational');
            $table->string('eduspecialization');
            $table->string('nostrificationattach_photo');
*/
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {   
        Schema::dropIfExists('mg_applications');
    }
}
