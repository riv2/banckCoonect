<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSoftDeleteToTableProfileDocsType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('profile_docs_type', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('student_request_types', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('profile_docs_type', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });

        Schema::table('student_request_types', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
    }
}
