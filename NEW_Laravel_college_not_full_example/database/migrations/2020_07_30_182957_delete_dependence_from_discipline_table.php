<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteDependenceFromDisciplineTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('disciplines', function (Blueprint $table) {
            $table->dropColumn('dependence');
            $table->dropColumn('dependence2');
            $table->dropColumn('dependence3');
            $table->dropColumn('dependence4');
            $table->dropColumn('dependence5');
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
            $table->string('dependence')->nullable();
            $table->string('dependence2')->nullable();
            $table->string('dependence3')->nullable();
            $table->string('dependence4')->nullable();
            $table->string('dependence5')->nullable();
        });
    }
}
