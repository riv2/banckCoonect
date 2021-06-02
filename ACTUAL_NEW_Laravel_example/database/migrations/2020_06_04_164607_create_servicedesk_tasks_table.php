<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicedeskTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('servicedesk_tasks', function (Blueprint $table) {
            $table->id('id');
            $table->string('type')->nullable();
            $table->bigInteger('pp_id');
            $table->bigInteger('creator_user_id');
            $table->bigInteger('doer_user_id')->nullable();
            $table->string('subject');
            $table->text('body')->nullable();
            $table->string('status')->default('new');
            $table->boolean('not_closed')->default(0);
            $table->decimal('estimate_time')->nullable();
            $table->decimal('fact_time')->nullable();
            $table->timestamp('deadline_at')->nullable();
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
        Schema::dropIfExists('servicedesk_tasks');
    }
}
