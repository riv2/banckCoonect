<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeEmployeesRequirements extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employees_requirements', function (Blueprint $table) {
            $table->dropColumn('description');
            $table->dropColumn('start_date');
            $table->dropColumn('end_date');
            $table->dropColumn('file');
            $table->string('field_type');
            $table->string('field_name');
            $table->string('category');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employees_requirements', function (Blueprint $table) {
            $table->text('description');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('file');
            $table->dropColumn('field_type');
            $table->dropColumn('field_name');
            $table->dropColumn('category');
        });
    }
}
