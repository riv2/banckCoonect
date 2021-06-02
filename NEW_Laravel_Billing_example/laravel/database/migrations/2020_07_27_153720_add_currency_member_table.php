<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCurrencyMemberTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('_members', 'currency_uniq')) {
            Schema::table('_members', function (Blueprint $table) {
                $table->string('currency_uniq')->nullable();
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
        Schema::table('_members', function (Blueprint $table) {
            $table->dropColumn('currency_uniq');
        });
    }
}
