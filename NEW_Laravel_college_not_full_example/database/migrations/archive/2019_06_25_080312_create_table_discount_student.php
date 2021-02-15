<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableDiscountStudent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discount_student', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('type_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->string('status')->default('new');
            $table->timestamp('valid_till')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('discount_student');
    }
}
