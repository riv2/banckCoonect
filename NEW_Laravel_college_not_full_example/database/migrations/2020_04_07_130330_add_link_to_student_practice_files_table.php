<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLinkToStudentPracticeFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('students_practice_files', function (Blueprint $table) {
            $table->enum('type', ['file', 'link'])->default('file')->after('discipline_id');
            $table->text('link')->nullable()->after('original_name');
            $table->string('file_name')->nullable()->change();
            $table->string('original_name')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('students_practice_files', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->dropColumn('link');
        });
    }
}
