<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateLinkTemplateInOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (config('app.gocpa_project') == 'cloud') {
            $params = [
                'utm_medium' => 'cpa',
                'utm_source' => 'partners',
                'utm_campaign' => '{$link_id}',
                'utm_content' => '{$partner_id}',
                'utm_term' => '{WEB_ID}',
                'click_id' => '{CLICK_ID}',
            ];

            $link_template = urldecode(http_build_query($params));
            DB::table('offers')->update(['link_template' => $link_template]);

            $params = [
                'utm_medium' => 'cpa',
                'utm_source' => 'pimpay',
                'utm_campaign' => '{$link_id}',
                'utm_content' => '{$partner_id}',
                'utm_term' => '{WEB_ID}',
                'click_id' => '{CLICK_ID}',
            ];
            $link_template = urldecode(http_build_query($params));
            DB::table('offers')->where('pp_id', '=', 79)->update(['link_template' => $link_template]);
        } elseif (config('app.gocpa_project') == 'cpadroid') {
            $params = [
                'utm_medium' => 'cpa',
                'utm_source' => '{$partner_hash_name}',
                'utm_campaign' => 'Pochta@Cash@lpCash@{$partner_hash_name}@Platform@{$link_id}@{WEB_ID}@{CLICK_ID}',
                'cpa_partner_id' => '{WEB_ID}',
                'cpa_click_id' => '{CLICK_ID}',
            ];
            $link_template = urldecode(http_build_query($params));
            DB::table('offers')->where('pp_id', '=', 16)->update(['link_template' => $link_template]);
        } else {
            throw new \Exception('unknown gocpa_project!');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('offers', function (Blueprint $table) {
            //
        });
    }
}
