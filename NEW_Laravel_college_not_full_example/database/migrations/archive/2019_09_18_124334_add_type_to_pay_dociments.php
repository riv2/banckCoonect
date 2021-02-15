<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTypeToPayDociments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("alter table `pay_documents` modify column `type` enum ('discipline', 'lecture', 'lecture_room', 'test', 'retake_test', 'registration_fee', 'to_balance')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("alter table `pay_documents` modify column `type` enum ('discipline', 'lecture', 'lecture_room', 'test', 'retake_test', 'registration_fee')");
    }
}
