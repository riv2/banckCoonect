<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDescriptionAndIsStudentFilledToDisciplineSyllabusDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('discipline_syllabus_documents', 'description')) {
            Schema::table('discipline_syllabus_documents', function (Blueprint $table) {
                $table->string('description')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('discipline_syllabus_documents', 'description')) {
            Schema::table('discipline_syllabus_documents', function (Blueprint $table) {
                $table->dropColumn('description');
            });
        }
    }
}
