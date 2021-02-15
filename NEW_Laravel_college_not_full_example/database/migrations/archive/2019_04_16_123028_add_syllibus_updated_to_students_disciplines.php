<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSyllibusUpdatedToStudentsDisciplines extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('students_disciplines', function (Blueprint $table) {
            $table->boolean('syllabus_updated')->default(false);
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
            $table->dropColumn('syllabus_updated');
        });
    }
}
