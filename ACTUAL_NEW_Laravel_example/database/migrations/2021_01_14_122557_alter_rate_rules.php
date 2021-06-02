<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterRateRules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rate_rules', function(Blueprint $table) {
            $table->string('progressive_param')->nullable()->after('offer_id')
                ->comment('Параметр, учитываемый в ставке(оборот, кол-во заказов)');
            $table->integer('progressive_value')->default(0)->after('offer_id')
            ->comment('Мин. значение для срабатывания прогрессивной ставки')
            ;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rate_rules', function(Blueprint $table) {
            $table->dropColumn('progressive_param');
            $table->dropColumn('progressive_value');
        });
    }
}
