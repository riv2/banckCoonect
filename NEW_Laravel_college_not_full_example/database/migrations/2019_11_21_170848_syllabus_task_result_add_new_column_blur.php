<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SyllabusTaskResultAddNewColumnBlur extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('syllabus_task_result', function (Blueprint $table) {
            $table->tinyInteger('blur')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('syllabus_task_result', function (Blueprint $table) {
            $table->dropColumn('blur');
        });
    }
}
