<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteColumnsFromEmployeesUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employees_users', function (Blueprint $table) {
            $table->dropColumn('citizenship');
            $table->dropColumn('address_registration');
            $table->dropColumn('address_residence');
            $table->dropColumn('home_phone');
            $table->dropColumn('doctype');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employees_users', function (Blueprint $table) {
            $table->integer('citizenship');
            $table->string('address_registration');
            $table->string('address_residence');
            $table->string('home_phone');
            $table->string('doctype');
        });
    }
}
