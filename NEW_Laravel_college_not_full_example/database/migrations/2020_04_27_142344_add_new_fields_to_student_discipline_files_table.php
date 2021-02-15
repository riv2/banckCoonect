<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewFieldsToStudentDisciplineFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('student_discipline_files', function (Blueprint $table) {
            $table->unsignedInteger('teacher_id')->nullable()->after('link');
            $table->foreign('teacher_id')->references('id')->on('users')->onDelete('cascade');

            $table->boolean('new_file')->default(true)->after('teacher_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('student_discipline_files', function (Blueprint $table) {
            $table->dropForeign('student_discipline_files_teacher_id_foreign');
            $table->dropColumn('teacher_id');

            $table->dropColumn('new_file');
        });
    }
}
