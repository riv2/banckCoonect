<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class StudentsDisciplinesAddNewColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('students_disciplines', function (Blueprint $table) {
            $table->tinyInteger('task_result')->nullable(true);
            $table->tinyInteger('task_result_points')->nullable(true);
            $table->string('task_result_letter',2)->nullable(true);
            $table->datetime('task_date')->nullable(true);
            $table->tinyInteger('task_result_trial')->nullable(true);
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
            $table->dropColumn('task_result');
            $table->dropColumn('task_result_points');
            $table->dropColumn('task_result_letter');
            $table->dropColumn('task_date');
            $table->dropColumn('task_result_trial');
        });
    }
}
