<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class VaPixel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pixel_log', function (Blueprint $table) {
            $table->char('parsed_client_id', 40)->nullable()->after('is_order');
            $table->char('parsed_partner_id', 40)->nullable()->after('parsed_client_id');
            $table->char('parsed_link_id', 40)->nullable()->after('parsed_partner_id');
            $table->char('parsed_order_id', 40)->nullable()->after('parsed_link_id');
            $table->char('parsed_click_id', 40)->nullable()->after('parsed_order_id');
            $table->char('parsed_web_id', 40)->nullable()->after('parsed_click_id');
            $table->char('saved_offer_id', 40)->nullable()->after('parsed_web_id');
            $table->char('saved_clicks_id', 40)->nullable()->after('saved_offer_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pixel_log', function (Blueprint $table) {
            //
        });
    }
}
