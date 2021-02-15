<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BcApplicationMgApplicationChangeIssueCityLength extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE bc_applications MODIFY COLUMN cityeducation TEXT");
        DB::statement("ALTER TABLE mg_applications MODIFY COLUMN cityeducation TEXT");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
