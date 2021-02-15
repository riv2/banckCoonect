<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToEmployeesUserPositions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('employees_users_positions');
        Schema::create('employees_users_positions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('position_id');
            $table->string('schedule')->nullable();
            $table->enum('employment', ['основная', 'совместительство'])->nullable();
            $table->enum('employment_form', ['Штатный сотрудник', 'сотрудник по совместительству'])->nullable();
            $table->decimal('price', 8, 2)->nullable();
            $table->decimal('salary', 8, 2)->nullable();
            $table->decimal('premium', 8, 2)->nullable();
            $table->string('organization')->nullable();
            $table->string('payroll_type')->nullable();
            $table->date('probation_from')->nullable();
            $table->date('probation_to')->nullable();
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
        Schema::dropIfExists('employees_users_positions');
        Schema::create('employees_users_positions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('position_id');
            $table->string('schedule')->nullable();
            $table->enum('employment', ['основная', 'совместительство'])->nullable();
            $table->decimal('price', 8, 2)->nullable();
            $table->decimal('salary', 8, 2)->nullable();
            $table->string('organization')->nullable();
            $table->string('payroll_type')->nullable();
            $table->timestamps();
        });
    }
}
