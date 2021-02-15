<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EmployeesAddNullableFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employees_departments', function (Blueprint $table) {
            $table->text('description')->nullable()->change();
        });
        Schema::table('employees_positions', function (Blueprint $table) {
            $table->text('description')->nullable()->change();
            $table->boolean('managerial')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employees_departments', function (Blueprint $table) {
            $table->text('description')->change();
        });
        Schema::table('employees_positions', function (Blueprint $table) {
            $table->text('description')->change();
            $table->dropColumn('managerial');
        });
    }
}
