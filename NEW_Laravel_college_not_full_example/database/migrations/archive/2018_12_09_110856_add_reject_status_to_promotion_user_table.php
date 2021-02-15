<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRejectStatusToPromotionUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE
                                    `promotion_user`
                                MODIFY COLUMN
                                    `status` enum(
                                        'moderation',
                                        'active',
                                        'block',
                                        'reject'
                                    )
                                NOT NULL AFTER `user_id`;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE
                                    `promotion_user`
                                MODIFY COLUMN
                                    `status` enum(
                                        'moderation',
                                        'active',
                                        'block'
                                    )
                                NOT NULL AFTER `user_id`;");
    }
}
