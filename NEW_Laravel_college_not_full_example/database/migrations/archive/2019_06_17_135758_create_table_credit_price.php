<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableCreditPrice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('credit_price', function (Blueprint $table) {
            $table->increments('id');

            //$table->string('name', 64)->nullable();
            $table->integer('price')->nullable();
            $table->integer('start_study_year')->nullable();
            $table->boolean('citizen');
            $table->boolean('bc'); //bc or mg
            $table->boolean('full-time');
            $table->integer('speciality_id')->nullable();
            //$table->string('description')->nullable();

            $table->timestamps();
        });

        // Insert some stuff
    DB::table('credit_price')->insert([
            'price' => 2500,
            'citizen' => true,
            'bc' => true,
            'full-time' => true
        ]);
    DB::table('credit_price')->insert([
            'price' => 5000,
            'citizen' => true,
            'bc' => true,
            'full-time' => true,
            'speciality_id' => 90
        ]);
    DB::table('credit_price')->insert([
            'price' => 2000,
            'start_study_year' => 2017,
            'citizen' => true,
            'bc' => true,
            'full-time' => true
        ]);
    DB::table('credit_price')->insert([
            'price' => 5000,
            'citizen' => false,
            'bc' => true,
            'full-time' => true
        ]);
    DB::table('credit_price')->insert([
            'price' => 10000,
            'citizen' => false,
            'bc' => true,
            'full-time' => true,
            'speciality_id' => 90
        ]);
    DB::table('credit_price')->insert([
            'price' => 5000,
            'start_study_year' => 2017,
            'citizen' => false,
            'bc' => true,
            'full-time' => true
        ]);
    DB::table('credit_price')->insert([
            'price' => 3400,
            'citizen' => true,
            'bc' => false,
            'full-time' => true
        ]);
    DB::table('credit_price')->insert([
            'price' => 6800,
            'citizen' => false,
            'bc' => false,
            'full-time' => true
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('credit_price');
    }
}
