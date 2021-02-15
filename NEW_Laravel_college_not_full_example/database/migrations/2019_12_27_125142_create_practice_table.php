<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePracticeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('practices')) {
            Schema::create('practices', function (Blueprint $table) {
                $table->increments('id');
                $table->string('organization_name');
                $table->string('organization_activity_type');
                $table->string('contract_number');
                $table->date('contract_start_date');
                $table->date('contract_end_date');
                $table->string('capacity');
                $table->timestamps();
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
        Schema::dropIfExists('practice');
    }
}
