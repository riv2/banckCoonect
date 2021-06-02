<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReestrsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reestrs', function (Blueprint $table) {
            $table->integer('reestr_id', true);
            $table->bigInteger('pp_id');
            $table->dateTime('datetime')->nullable();
            $table->decimal('total', 16)->nullable()->default(0.00);
            $table->decimal('payed', 16)->nullable()->default(0.00);
            $table->integer('status')->nullable()->default(0);
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
        Schema::dropIfExists('reestrs');
    }
}
