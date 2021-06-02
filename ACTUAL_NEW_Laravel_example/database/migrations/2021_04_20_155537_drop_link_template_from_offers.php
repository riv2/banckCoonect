<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropLinkTemplateFromOffers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->dropColumn('link_template');
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
            $table->string('link_template')
                ->nullable()
                ->default('utm_source=partners&utm_medium=cpa&utm_campaign=LINK_ID&utm_content=PARTNER_ID&utm_click=CLICK_ID&utm_web=WEB_ID')
                ->after('image');
        });
    }
}
