<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeSubmodules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('submodules', function (Blueprint $table) {
            $table->string('name_kz', 255)->nullable()->change();
            $table->string('name_en', 255)->nullable()->change();
            $table->unsignedInteger('ects')->nullable()->comment('кредиты европейские')->change();

            $table->string('dependence', 255)->nullable()->change();
            $table->string('dependence2', 255)->nullable()->change();
            $table->string('dependence3', 255)->nullable()->change();
            $table->string('dependence4', 255)->nullable()->change();
            $table->string('dependence5', 255)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('submodules', function (Blueprint $table) {
            $table->string('name_kz', 255)->change();
            $table->string('name_en', 255)->change();
            $table->unsignedInteger('ects')->comment('')->change();

            $table->string('dependence', 255)->change();
            $table->string('dependence2', 255)->change();
            $table->string('dependence3', 255)->change();
            $table->string('dependence4', 255)->change();
            $table->string('dependence5', 255)->change();
        });
    }
}
