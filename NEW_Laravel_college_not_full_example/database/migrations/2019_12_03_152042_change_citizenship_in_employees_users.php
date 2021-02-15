<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeCitizenshipInEmployeesUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('employees_users');
        Schema::create('employees_users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->enum('status', ['кандидат', 'сотрудник', 'декретный отпуск', 'уволен']);
            $table->integer('citizenship');
            $table->string('address_registration');
            $table->string('address_residence');
            $table->string('home_phone')->nullable();
            $table->string('doctype');
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
        Schema::dropIfExists('employees_users');
        Schema::create('employees_users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->enum('status', ['кандидат', 'сотрудник', 'декретный отпуск', 'уволен']);
            $table->string('citizenship');
            $table->string('address_registration');
            $table->string('address_residence');
            $table->string('home_phone')->nullable();
            $table->string('doctype');
            $table->timestamps();
        });
    }
}
