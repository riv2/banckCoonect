<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProfileTeachersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('profile_teachers')) {
            Schema::create('profile_teachers', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id')->references('id')->on('users');
                $table->string('photo')->nullable()->default(null);
                $table->integer('iin')->nullable()->default(null);
                $table->string('fio')->nullable()->default(null);
                $table->string('bdate')->nullable()->default(null);
                $table->integer('pass')->nullable()->default(null);
                $table->string('docnumber')->nullable()->default(null);
                $table->string('issuing')->nullable()->default(null);
                $table->string('issuedate')->nullable()->default(null);
                $table->integer('sex')->nullable()->default(null);
                $table->string('mobile')->nullable()->default(null);
                $table->string('front_id_photo')->nullable()->default(null);
                $table->string('back_id_photo')->nullable()->default(null);
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
        Schema::dropIfExists('profile_teachers');
    }
}
