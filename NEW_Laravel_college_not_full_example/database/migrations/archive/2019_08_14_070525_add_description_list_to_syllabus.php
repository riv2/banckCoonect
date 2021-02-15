<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDescriptionListToSyllabus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('syllabus', function (Blueprint $table) {
            $table->longText('teoretical_description')->nullable()->after('sro_hours');
            $table->longText('practical_description')->nullable()->after('teoretical_description');
            $table->longText('sro_description')->nullable()->after('practical_description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('syllabus', function (Blueprint $table) {
            $table->dropColumn('teoretical_description');
            $table->dropColumn('practical_description');
            $table->dropColumn('sro_description');
        });
    }
}
