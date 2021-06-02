<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEvidenceToBannedLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('banned_links', function (Blueprint $table) {
            $table->foreignId('pp_id')->nullable()->index()->after('web_id');
            $table->text('evidence')->nullable()->after('comment');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('banned_links', function (Blueprint $table) {
            $table->dropColumn('pp_id');
            $table->dropColumn('evidence');
        });
    }
}
