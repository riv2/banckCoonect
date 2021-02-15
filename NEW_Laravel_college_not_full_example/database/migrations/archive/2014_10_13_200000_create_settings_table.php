<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('settings')) {
            Schema::create('settings', function (Blueprint $table) {
                $table->increments('id');

                $table->integer('group_id')->nullable();
                $table->string('site_style')->nullable();
                $table->string('site_name')->nullable();
                $table->string('site_email')->nullable();
                $table->string('site_logo')->nullable();
                $table->string('site_favicon')->nullable();
                $table->string('site_description')->nullable();
                $table->text('site_keywords')->nullable();
                $table->text('site_header_code')->nullable();
                $table->text('site_footer_code')->nullable();
                $table->string('site_copyright')->nullable();
                $table->text('footer_widget1')->nullable();
                $table->text('footer_widget2')->nullable();
                $table->text('footer_widget3')->nullable();
                $table->text('addthis_share_code')->nullable();
                $table->text('disqus_comment_code')->nullable();
                $table->string('social_facebook')->nullable();
                $table->string('social_twitter')->nullable();
                $table->string('social_linkedin')->nullable();
                $table->string('social_gplus')->nullable();
                $table->string('about_us_title')->nullable();
                $table->text('about_us_description')->nullable();
                $table->string('about_us_title_ru')->nullable();
                $table->text('about_us_description_ru')->nullable();
                $table->string('careers_with_us_title')->nullable();
                $table->text('careers_with_us_description')->nullable();
                $table->string('terms_conditions_title')->nullable();
                $table->text('terms_conditions_description')->nullable();
                $table->string('privacy_policy_title')->nullable();
                $table->string('prices_title')->nullable();
                $table->text('prices_description')->nullable();
                $table->text('privacy_policy_description')->nullable();
                $table->string('currency_sign')->nullable();
                $table->integer('clients_count')->nullable();
                $table->integer('hours_count')->nullable();
                $table->integer('projects_count')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
}
