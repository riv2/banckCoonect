<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeControlFormInDisciplines extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("alter table `disciplines` modify column `control_form` enum ('test', 'write', 'report', 'score', 'traditional')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("alter table `disciplines` modify column `control_form` enum ('test', 'write', 'report', 'score', 'traditional')");
    }
}
