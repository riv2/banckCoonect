<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBcApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('bc_applications')) {
            Schema::create('bc_applications', function (Blueprint $table) {
                $table->integer('id', true);

                $table->integer('nationality_id')->nullable();
                $table->integer('citizenship_id')->nullable();
                $table->integer('family_status')->nullable();
                $table->integer('region_id')->nullable();
                $table->integer('city_id')->nullable();
                $table->string('street')->nullable();
                $table->string('building_number', 32)->nullable();
                $table->string('apartment_number', 32)->nullable();
                $table->string('residence_registration_photo', 64)->nullable();
                $table->string('military_photo', 64)->nullable();
                $table->string('r086_photo', 64)->nullable();
                $table->string('r063_photo', 64)->nullable();
                $table->string('education', 64)->nullable();
                $table->string('ikt', 20)->nullable();
                $table->integer('ent_total')->nullable();
                $table->string('ent_name_1', 32)->nullable();
                $table->integer('ent_val_1')->nullable();
                $table->string('ent_name_2', 32)->nullable();
                $table->integer('ent_val_2')->nullable();
                $table->string('ent_name_3', 32)->nullable();
                $table->integer('ent_val_3')->nullable();
                $table->string('ent_name_4', 32)->nullable();
                $table->integer('ent_val_4')->nullable();
                $table->string('ent_name_5', 32)->nullable();
                $table->integer('ent_val_5')->nullable();
                $table->string('ent_lang', 25)->nullable();
                $table->string('bceducation', 30)->nullable();
                $table->integer('user_id')->nullable();
                $table->string('numeducation', 32)->nullable();
                $table->string('sereducation', 64)->nullable();
                $table->string('nameeducation', 64)->nullable();
                $table->date('dateeducation')->nullable();
                $table->string('cityeducation', 32)->nullable();
                $table->string('atteducation_photo')->nullable();
                $table->integer('kzornot')->nullable();
                $table->string('eduspecialty', 64)->nullable();
                $table->string('typevocational', 64)->nullable();
                $table->string('edudegree', 64)->nullable();
                $table->string('eduspecialization', 64)->nullable();
                $table->string('nostrification', 64)->nullable();
                $table->string('nostrificationattach_photo', 64)->nullable();

                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bc_applications');
    }
}
