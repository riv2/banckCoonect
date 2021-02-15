<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDailyIdFieldToDocsEnquire extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('docs_enquire', function (Blueprint $table) {
            $table->boolean('daily_id')->default(0)->after('doctype');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('docs_enquire', function (Blueprint $table) {
            $table->dropColumn('daily_id');
        });
    }
}
