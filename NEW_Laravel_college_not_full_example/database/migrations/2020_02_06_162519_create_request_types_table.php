<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequestTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_request_types', function (Blueprint $table) {
            $table->increments('id');

            $table->string('key', 64)->unique();

            $table->string('name_ru', 64)->nullable();
            $table->string('name_kz', 64)->nullable();
            $table->string('name_en', 64)->nullable();

            $table->string('template_doc', 96)->nullable();

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
        Schema::dropIfExists('request_types');
    }
}
