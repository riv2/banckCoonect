<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PayDocumentsAddNewType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE `pay_documents` MODIFY COLUMN `type` ENUM(\'discipline\', \'lecture\', \'lecture_room\', \'test\', \'retake_test\', \'registration_fee\', \'to_balance\', \'wifi\', \'retake_exam\', \'registration\') NULL ');
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE `pay_documents` MODIFY COLUMN `type` ENUM(\'discipline\', \'lecture\', \'lecture_room\', \'test\', \'retake_test\', \'registration_fee\', \'to_balance\', \'wifi\', \'retake_exam\') NULL ');
    }
}
