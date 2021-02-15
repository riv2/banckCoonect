<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTableStudentFinanceNomenclature extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_finance_nomenclature', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('finance_nomenclature_id');
            $table->unsignedInteger('cost');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('finance_nomenclature_id')->references('id')->on('finance_nomenclatures');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('student_finance_nomenclature');
    }
}
