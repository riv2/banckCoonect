<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToAppeals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('appeals', function (Blueprint $table) {
            $table->renameColumn('resolution', 'resolution_action');

        });

        Schema::table('appeals', function (Blueprint $table) {
            $table->unsignedTinyInteger('added_value')->after('resolution_action')->nullable();
            $table->unsignedInteger('quiz_result_id')->after('student_discipline_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('appeals', function (Blueprint $table) {
            $table->dropColumn('quiz_result_id');
            $table->dropColumn('added_value');
            $table->renameColumn('resolution_action', 'resolution');
        });
    }
}
