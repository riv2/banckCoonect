<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCommentsToStudentsDisciplines extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('students_disciplines', function (Blueprint $table) {
            $table->boolean('payed')->default(0)->comment('Полностью оплачена')->change();
            $table->integer('payed_credits')->nullable()->comment('Оплачено кредитов')->change();
            $table->boolean('approved')->default(0)->comment('')->change();
            $table->unsignedInteger('iteration')->default(1)->comment('Кол-во сдач, пересдачь')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('students_disciplines', function (Blueprint $table) {
            $table->integer('payed')->default(0)->comment('')->change();
            $table->integer('payed_credits')->nullable()->comment('')->change();
            $table->integer('approved')->default(0)->comment('')->change();
            $table->integer('iteration')->default(1)->comment('')->change();
        });
    }
}
