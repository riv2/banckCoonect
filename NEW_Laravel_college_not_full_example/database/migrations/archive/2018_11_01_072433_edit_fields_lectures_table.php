<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditFieldsLecturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lectures', function (Blueprint $table) {
            $table->dropColumn('count_minutes');
            $table->smallInteger('duration')->default(0)->after('start')->comment('Продолжительность (академ часов)');
            DB::statement("alter table `lectures` modify column `type` enum ('online', 'offline', 'all')");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lectures', function (Blueprint $table) {
            $table->dropColumn('duration');
            $table->smallInteger('count_minutes')->default(0)->comment('Продолжительность в минутах');
            DB::statement("alter table `lectures` modify column `type` enum ('online', 'offline')");
        });
    }
}
