<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRetakyKgeTypeToPayDocuments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("alter table `pay_documents` modify column `type` enum ('discipline', 'lecture', 'lecture_room', 'test', 'retake_test', 'retake_kge', 'registration_fee')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pay_documents', function (Blueprint $table) {
            DB::statement("alter table `pay_documents` modify column `type` enum ('discipline', 'lecture', 'lecture_room', 'test', 'retake_test', 'registration_fee')");
        });
    }
}
