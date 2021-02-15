<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddKtNumberToMgApplications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mg_applications', function (Blueprint $table) {
            $table->string('kt_number')->nullable()->after('eng_certificate_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mg_applications', function (Blueprint $table) {
            $table->dropColumn('kt_number');
        });
    }
}
