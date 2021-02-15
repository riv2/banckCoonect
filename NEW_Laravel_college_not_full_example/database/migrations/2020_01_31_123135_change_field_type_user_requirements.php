<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeFieldTypeUserRequirements extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employees_user_requirements', function (Blueprint $table) {
            $table->integer('requirement_id');
            $table->text('json_content')->nullable();
            $table->dropColumn('field_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employees_user_requirements', function (Blueprint $table) {
            $table->dropColumn('requirement_id');
            $table->string('field_name');
        });
    }
}
