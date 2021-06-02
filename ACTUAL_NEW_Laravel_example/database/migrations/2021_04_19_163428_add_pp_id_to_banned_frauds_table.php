<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPpIdToBannedFraudsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('banned_frauds', function (Blueprint $table) {
            $table->foreignId('pp_id')->nullable()->index()->after('offer_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('banned_frauds', function (Blueprint $table) {
            $table->dropColumn('pp_id');
        });
    }
}
