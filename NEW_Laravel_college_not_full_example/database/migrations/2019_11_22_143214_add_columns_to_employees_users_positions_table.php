<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToEmployeesUsersPositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employees_users_positions', function (Blueprint $table) {
            $table->string('schedule');
            $table->enum('employment', ['основная', 'совместительство']);
            $table->decimal('price', 8, 2);
            $table->decimal('salary', 8, 2);
            $table->string('organization');
            $table->string('payroll_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employees_users_positions', function (Blueprint $table) {
            $table->dropColumn('schedule');
            $table->dropColumn('employment');
            $table->dropColumn('price');
            $table->dropColumn('salary');
            $table->dropColumn('organization');
            $table->dropColumn('payroll_type');
        });
    }
}
