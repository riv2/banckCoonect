<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeTableAcademicDegree extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('academic_degree', function (Blueprint $table) {
            $table->string('short_name',50)->nullable(true);
            $table->string('short_namekz',50)->nullable(true);
            $table->string('type',50)->nullable(true);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('academic_degree', function (Blueprint $table) {
            $table->dropColumn('short_name');
            $table->dropColumn('short_namekz');
            $table->dropColumn('type');
        });
    }
}
