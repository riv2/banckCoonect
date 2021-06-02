<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicedeskTaskCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('servicedesk_task_comments', function (Blueprint $table) {
            $table->id('id');
            $table->bigInteger('servicedesk_task_id');
            $table->bigInteger('partner_id');
            $table->boolean('is_public')->default(0);
            $table->text('body');
            $table->json('attach')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('servicedesk_task_comments');
    }
}
