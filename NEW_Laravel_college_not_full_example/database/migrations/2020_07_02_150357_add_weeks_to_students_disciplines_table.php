<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWeeksToStudentsDisciplinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('students_disciplines', function (Blueprint $table) {
            $table->string('week16_result')->nullable();
            $table->string('week17_result')->nullable();
            $table->string('week18_result')->nullable();
            $table->string('week19_result')->nullable();
            $table->string('week20_result')->nullable();
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
            $table->dropColumn('week16_result');
            $table->dropColumn('week17_result');
            $table->dropColumn('week18_result');
            $table->dropColumn('week19_result');
            $table->dropColumn('week20_result');
        });
    }
}
