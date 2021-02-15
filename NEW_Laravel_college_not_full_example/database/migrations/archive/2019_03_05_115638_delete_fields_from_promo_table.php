<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteFieldsFromPromoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(Schema::hasTable('promo')) {
            Schema::table('promo', function (Blueprint $table) {
                if(Schema::hasColumn('promo','iin'))
                    $table->dropColumn('iin');
                if(Schema::hasColumn('promo','bdate'))
                    $table->dropColumn('bdate');
                if(Schema::hasColumn('promo','pass'))
                    $table->dropColumn('pass');
                if(Schema::hasColumn('promo','docnumber'))
                    $table->dropColumn('docnumber');
                if(Schema::hasColumn('promo','issuing'))
                    $table->dropColumn('issuing');
                if(Schema::hasColumn('promo','issuedate'))
                    $table->dropColumn('issuedate');
                if(Schema::hasColumn('promo','expire_date'))
                    $table->dropColumn('expire_date');
                if(Schema::hasColumn('promo','sex'))
                    $table->dropColumn('sex');
                if(Schema::hasColumn('promo','front_id_photo'))
                    $table->dropColumn('front_id_photo');
                if(Schema::hasColumn('promo','back_id_photo'))
                    $table->dropColumn('back_id_photo');
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
        /*Schema::table('promo', function (Blueprint $table) {
            //
        });*/
    }
}
