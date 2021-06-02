<?php

use App\Models\Pp;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ChangeLangInPpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pp', function (Blueprint $table) {
            $table->dropColumn('lang');
        });
        Schema::table('pp', function (Blueprint $table) {
            $table->json('lang')->nullable()->after('color4');
        });

        $pps = Pp::all();
        foreach ($pps as $pp) {
            $pp->lang = ['ru' => true, 'en' => true, 'es' => true];
            $pp->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pp', function (Blueprint $table) {
            $table->dropColumn('lang');
        });
        Schema::table('pp', function (Blueprint $table) {
            $table->string('lang')->after('color4');
        });
    }
}
