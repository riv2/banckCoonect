<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPayDocumentsType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE
                                    `pay_documents`
                                MODIFY COLUMN
                                    `type` enum(
                                        'discipline',
                                        'lecture',
                                        'lecture_room',
                                        'test',
                                        'retake_test'
                                    )
                                NOT NULL AFTER `status`;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE
                                    `pay_documents`
                                MODIFY COLUMN
                                    `type` enum(
                                        'discipline',
                                        'lecture',
                                        'lecture_room',
                                        'test'
                                    )
                                NOT NULL AFTER `status`;");
    }
}
