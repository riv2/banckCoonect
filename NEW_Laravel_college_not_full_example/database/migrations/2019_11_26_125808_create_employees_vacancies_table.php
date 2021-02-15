<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeesVacanciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('employees_vacancies')) {
            Schema::create('employees_vacancies', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('position_id');
                $table->integer('schedule_id');
                $table->enum('employment', ['основная', 'совместительство']);
                $table->decimal('price', 8, 2);
                $table->decimal('salary', 8, 2);
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
        Schema::dropIfExists('employees_vacancies');
    }
}
