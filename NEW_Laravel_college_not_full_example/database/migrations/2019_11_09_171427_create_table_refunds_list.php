<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableRefundsList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('refunds_list')) {
            Schema::create('refunds_list', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('bank_id')->nullable();
                $table->string('user_iban')->nullable();
                $table->integer('user_id');
                $table->integer('tiyn')->default(1000000)->comment('Сумма возврата в тиынах (копейках)');
                $table->enum('status', ['reference', 'new', 'processing', 'bank_processing', 'success', 'returned'])->default('reference');
                $table->string('order_number')->nullable()->comment('Номер заявки в 1с');
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
        Schema::dropIfExists('refunds_list');
    }
}
