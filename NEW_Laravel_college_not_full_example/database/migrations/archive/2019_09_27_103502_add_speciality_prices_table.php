<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSpecialityPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('speciality_prices', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('speciality_id');
            $table->string('study_form', 10);
            $table->string('base_education', 20);
            $table->string('price_type', 25);
            $table->unsignedInteger('price');
            $table->timestamps();

            $table->foreign('speciality_id')->references('id')->on('specialities')->onDelete('cascade');
            $table->unique(['speciality_id', 'study_form', 'base_education', 'price_type'], 'unique_price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('speciality_prices');
    }
}
