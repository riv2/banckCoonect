<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDependesesToDisciplinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('disciplines', function (Blueprint $table) {
            
            $table->string('syllabus')->nullable()->change();

            $table->string('num_ru')->comment('шифр на русском')->change();
            $table->string('num_kz')->comment('шифр на казахском')->change();
            $table->string('num_en')->comment('шифр на англ')->change();

            $table->string('dependence2')->after('dependence')->nullable()->comment('пререквезиты2 через запятую');
            $table->string('dependence3')->after('dependence2')->nullable()->comment('пререквезиты3 через запятую');
            $table->string('dependence4')->after('dependence3')->nullable()->comment('пререквезиты4 через запятую');
            $table->string('dependence5')->after('dependence4')->nullable()->comment('пререквезиты5 через запятую');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('disciplines', function (Blueprint $table) {
            $table->dropColumn('dependence2');
            $table->dropColumn('dependence3');
            $table->dropColumn('dependence4');
            $table->dropColumn('dependence5');
            
        });
    }
}
