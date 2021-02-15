<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeManagerUserToManagerPosition extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employees_departments', function (Blueprint $table) {
            $table->integer('manager_position_id')->nullable();
            $table->dropColumn('manager_user_id');
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
            $table->dropColumn('manager_position_id');
            $table->integer('manager_user_id')->nullable();
        });
    }
}
