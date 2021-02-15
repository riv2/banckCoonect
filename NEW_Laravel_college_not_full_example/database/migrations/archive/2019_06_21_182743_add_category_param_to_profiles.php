<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCategoryParamToProfiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("alter table `profiles` modify column `category` enum ('matriculant', 'standart', 'transit', 'trajectory_change') DEFAULT 'matriculant'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("alter table `profiles` modify column `category` enum ('standart', 'transit', 'trajectory_change') DEFAULT 'standart'");
    }
}
