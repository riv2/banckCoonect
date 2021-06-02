<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToOffers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('offers', function (Blueprint $table) {
            $values = [
                'active',
                'closed',
                'active_only_for_user_ids',
                'closed_only_for_user_ids',
            ];
            $table->enum('status', $values)->default('active')->after('web_id_name');
            $table->json('for_user_ids')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('for_user_ids');
        });
    }
}
