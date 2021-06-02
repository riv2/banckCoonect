<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ExtCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('_companies', 'hidden')) {
            Schema::table('_companies', function (Blueprint $table) {
                $table->string('hidden')->nullable();
            });
        }
        if (!Schema::hasColumn('_companies', 'allow')) {
            Schema::table('_companies', function (Blueprint $table) {
                $table->string('allow')->nullable();
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
        Schema::table('_companies', function (Blueprint $table) {
            $table->dropColumn('owner');
            $table->dropColumn('hidden');
            $table->dropColumn('allow');
        });
    }
}
