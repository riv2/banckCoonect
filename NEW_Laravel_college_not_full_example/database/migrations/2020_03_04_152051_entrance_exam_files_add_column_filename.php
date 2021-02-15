<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EntranceExamFilesAddColumnFilename extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if ( !Schema::hasColumn('entrance_exam_files', 'filename') ) {
            Schema::table('entrance_exam_files', function (Blueprint $table)
            {
                $table->string('filename',255)->nullable(true)->after('name');
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
        if ( Schema::hasColumn('entrance_exam_files', 'filename') ) {
            Schema::table('entrance_exam_files', function (Blueprint $table)
            {
                $table->dropColumn('filename');
            });
        }
    }
}
