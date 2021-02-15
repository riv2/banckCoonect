<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixUpdatedAtOnStudentsDisciplines extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('students_disciplines', function (Blueprint $table) {
            $table->renameColumn('udated_at', 'updated_at');
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
            $table->renameColumn('updated_at', 'udated_at');
        });
    }
}
