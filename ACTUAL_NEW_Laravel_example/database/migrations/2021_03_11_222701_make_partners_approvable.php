<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakePartnersApprovable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->boolean('flag_approvable')->after('web_id_name')->default(false);
        });

        Schema::create('offers_partners_approves', function (Blueprint $table) {
            $table->foreignId('offer_id');
            $table->foreignId('partner_id');
            $table->enum('status', ['pending', 'approved', 'declined'])->default('pending');
            $table->timestamps();
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
            $table->dropColumn('flag_approvable');
        });

        Schema::drop('offers_partners_approves');
    }
}
