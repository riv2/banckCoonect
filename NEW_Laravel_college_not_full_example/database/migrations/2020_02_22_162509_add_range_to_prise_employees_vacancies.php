<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRangeToPriseEmployeesVacancies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE employees_vacancies MODIFY COLUMN price decimal(12,2)");
        DB::statement("ALTER TABLE employees_vacancies MODIFY COLUMN salary decimal(12,2)");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE employees_vacancies MODIFY COLUMN price decimal(8,2)");
        DB::statement("ALTER TABLE employees_vacancies MODIFY COLUMN salary decimal(8,2)");
    }
}
