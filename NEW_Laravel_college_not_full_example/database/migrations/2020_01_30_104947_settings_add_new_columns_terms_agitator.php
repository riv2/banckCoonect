<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SettingsAddNewColumnsTermsAgitator extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasColumn('settings', 'agitator_terms_conditions_description')){
            Schema::table('settings', function (Blueprint $table)
            {
                $table->longText('agitator_terms_conditions_description')->nullable(true);
            });
        }
        if(!Schema::hasColumn('settings', 'agitator_terms_conditions_description_kz')){
            Schema::table('settings', function (Blueprint $table)
            {
                $table->longText('agitator_terms_conditions_description_kz')->nullable(true);
            });
        }
        if(!Schema::hasColumn('settings', 'agitator_terms_conditions_description_en')){
            Schema::table('settings', function (Blueprint $table)
            {
                $table->longText('agitator_terms_conditions_description_en')->nullable(true);
            });
        }
    }

    /**+
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('agitator_terms_conditions_description');
            $table->dropColumn('agitator_terms_conditions_description_kz');
            $table->dropColumn('agitator_terms_conditions_description_en');
        });
    }
}
