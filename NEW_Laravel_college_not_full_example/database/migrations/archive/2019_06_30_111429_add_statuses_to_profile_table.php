<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusesToProfileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("alter table `profiles` modify column `category` enum ('matriculant', 'standart', 'standart_recount', 'transit', 'trajectory_change', 'retake_ent', 'transfer') DEFAULT 'matriculant'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("alter table `profiles` modify column `category` enum ('matriculant', 'standart', 'transit', 'trajectory_change') DEFAULT 'matriculant'");
    }
}
