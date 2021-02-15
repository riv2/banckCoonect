<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeesVacancyRequirementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('employees_vacancy_requirements')) {
            Schema::create('employees_vacancy_requirements', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('vacancy_id');
                $table->integer('requirement_id');
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
        Schema::dropIfExists('employees_vacancy_requirements');
    }
}
