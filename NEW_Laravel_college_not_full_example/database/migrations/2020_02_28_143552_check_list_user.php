<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CheckListUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('check_list_user')) {
            Schema::create('check_list_user', function (Blueprint $table) {

                $table->increments('id');

                $table->integer('user_id')->nullable(false);
                $table->integer('check_list_id')->nullable(false);

                $table->tinyInteger('prerequisites_active')->default(0);
                $table->tinyInteger('interview_active')->default(0);

                $table->timestamps();
                $table->softDeletes();

            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('check_list_user');
    }
}
