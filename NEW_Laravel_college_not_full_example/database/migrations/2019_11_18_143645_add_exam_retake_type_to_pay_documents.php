<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExamRetakeTypeToPayDocuments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `pay_documents` CHANGE `type` `type` ENUM('discipline','lecture','lecture_room','test','retake_test','registration_fee','to_balance','wifi', 'retake_exam') NULL DEFAULT NULL; ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE `pay_documents` CHANGE `type` `type` ENUM('discipline','lecture','lecture_room','test','retake_test','registration_fee','to_balance','wifi') NULL DEFAULT NULL; ");
    }
}
