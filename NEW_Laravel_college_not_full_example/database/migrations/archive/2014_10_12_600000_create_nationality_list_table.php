<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNationalityListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('nationality_list')) {
            Schema::create('nationality_list', function (Blueprint $table) {
                $table->integer('id', true);

                $table->string('name', 64)->nullable();
                $table->string('name_ru', 64)->nullable();
                $table->string('name_kz', 90)->nullable();
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
        Schema::dropIfExists('nationality_list');
    }
}
