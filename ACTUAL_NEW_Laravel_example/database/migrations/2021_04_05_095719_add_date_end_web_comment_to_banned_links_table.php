<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDateEndWebCommentToBannedLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('banned_links', function (Blueprint $table) {
            $table->string('web_id')->nullable()->after('link_id');
            $table->date('date_end')->nullable()->after('date_start');
            $table->text('comment')->nullable()->after('date_end');
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
        Schema::table('banned_links', function (Blueprint $table) {
            $table->dropColumn('web_id');
            $table->dropColumn('date_end');
            $table->dropColumn('comment');
        });
    }
}
