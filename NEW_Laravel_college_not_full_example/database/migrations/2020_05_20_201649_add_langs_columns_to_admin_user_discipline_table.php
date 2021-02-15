<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLangsColumnsToAdminUserDisciplineTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('admin_user_discipline', function (Blueprint $table) {
            $table->boolean('kz_lang')->nullable();
            $table->boolean('ru_lang')->nullable();
            $table->boolean('en_lang')->nullable();

            $table->integer('employees_user_position_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('admin_user_discipline', function (Blueprint $table) {
            $table->dropColumn('kz_lang');
            $table->dropColumn('ru_lang');
            $table->dropColumn('en_lang');
            $table->dropColumn('employees_user_position_id');
        });
    }
}
