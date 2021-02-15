<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeEnumSymbolInEmployeesUsersPositionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE employees_users_positions MODIFY COLUMN employment_form ENUM('Штатный сотрудник', 'Сотрудник по совместительству')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE employees_users_positions MODIFY COLUMN employment_form ENUM('Штатный сотрудник', 'сотрудник по совместительству')");
    }
}
