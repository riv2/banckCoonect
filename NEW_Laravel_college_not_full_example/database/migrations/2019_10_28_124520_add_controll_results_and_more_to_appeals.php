<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddControllResultsAndMoreToAppeals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('appeals', function (Blueprint $table) {
            $table->string('status', 10)->after('type');
            $table->string('resolution', 10)->after('status')->nullable();

            $table->unsignedTinyInteger('control_result')->after('resolution');
            $table->unsignedTinyInteger('control_result_points')->after('control_result');
            $table->string('control_result_letter', 2)->after('control_result_points');
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
            $table->dropColumn('status');
            $table->dropColumn('resolution');
            $table->dropColumn('control_result');
            $table->dropColumn('control_result_points');
            $table->dropColumn('control_result_letter');
        });
    }
}
