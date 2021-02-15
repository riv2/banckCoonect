<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddManualOldMarkFieldToProfileDocsTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('profile_docs_type', function (Blueprint $table) {
            $table->boolean('manual_old_mark')->default(false);
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
            $table->dropColumn('manual_old_mark');
        });
    }
}
