<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusProfileDocsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('profile_docs', function (Blueprint $table) {
            $table->enum('status', ['moderation', 'allow', 'disallow'])
                ->default('moderation')->after('doc_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('profile_docs', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
