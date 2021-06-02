<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMaterialIdToXmlfeed extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::table('xmlfeed_categories', function (Blueprint $table) {
        //     $table->bigInteger("offer_material_id")->index()->after("id");
        //     $table->dropUnique("xmlfeed_categories_pp_id_category_id_unique");
        //     $table->unique(["pp_id", "category_id", "offer_material_id"], "uniq_key");
        // });
        // Schema::table('xmlfeed_offers', function (Blueprint $table) {
        //     $table->foreignId("offer_material_id")->index()->after("id");
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::table('xmlfeed_categories', function (Blueprint $table) {
        //     $table->dropColumn("offer_material_id");
        //     $table->dropUnique("uniq_key");
        //     $table->unique(["pp_id", "category_id"]);
        // });
        // Schema::table('xmlfeed_offers', function (Blueprint $table) {
        //     $table->dropColumn("offer_material_id");
        // });
    }
}
