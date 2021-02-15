<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDisciplineIdToStudyGroupTeacher extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('study_group_teacher', function (Blueprint $table) {
            $table->integer('discipline_id')->after('study_group_id');
            $table->foreign('discipline_id')->references('id')->on('disciplines')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('study_group_teacher', function (Blueprint $table) {
            $table->dropForeign('study_group_teacher_discipline_id_foreign');
            $table->dropColumn('discipline_id');
        });
    }
}
