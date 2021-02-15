<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableCoursesStudent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('courses_student')) {
            Schema::create('courses_student', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('courses_id');
                $table->unsignedInteger('user_id');
                $table->string('language', 255)->nullable(false);
                $table->text('schedule')->nullable(true);
                $table->integer('cost')->nullable(false);
                $table->enum('pay_method', [
                    'balance',
                    'epay'
                ])->default('balance');
                $table->enum('payed', [
                    'yes',
                    'no'
                ])->default('no');
                $table->enum('status', [
                    'active',
                    'inactive',
                    'processing'
                ])->default('active');

                $table->timestamps();
                $table->softDeletes();

                $table->foreign('courses_id')->references('id')->on('courses')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

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
        Schema::dropIfExists('courses_student');
    }

}
