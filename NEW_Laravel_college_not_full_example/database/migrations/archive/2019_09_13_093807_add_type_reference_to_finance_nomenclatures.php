<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTypeReferenceToFinanceNomenclatures extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE `finance_nomenclatures` MODIFY COLUMN `type` ENUM(\'fee\', \'other\', \'reference\') NULL ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE `finance_nomenclatures` MODIFY COLUMN `type` ENUM(\'fee\', \'other\') NULL ');
    }
}
