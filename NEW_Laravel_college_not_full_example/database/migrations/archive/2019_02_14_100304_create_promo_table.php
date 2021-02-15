<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePromoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('promo')) {
            Schema::create('promo', function (Blueprint $table) {
                $table->increments('id');
                $table->string('iin', 24)->nullable();
                $table->string('fio')->nullable();
                $table->date('bdate')->nullable();
                $table->integer('pass')->nullable();
                $table->string('docnumber')->nullable();
                $table->string('issuing')->nullable();
                $table->date('issuedate')->nullable();
                $table->date('expire_date')->nullable();
                $table->integer('sex')->nullable();
                $table->string('front_id_photo', 128)->nullable();
                $table->string('back_id_photo', 128)->nullable();

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
        Schema::dropIfExists('promo');
    }
}
