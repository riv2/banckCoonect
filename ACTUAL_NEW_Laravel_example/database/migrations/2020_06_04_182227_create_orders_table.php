<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->nullable()->default('');
            $table->foreignId('offer_id')->index();
            $table->dateTime('datetime')->nullable();
            $table->foreignId('partner_id')->nullable()->index();
            $table->foreignId('pp_id')->nullable()->index();
            $table->foreignId('category_id')->nullable()->index();
            $table->foreignId('landing_id')->nullable();
            $table->foreignId('link_id')->nullable()->index();
            $table->string('click_id')->nullable();
            $table->string('web_id')->nullable();
            $table->string('client_id')->nullable();
            $table->foreignId('pixel_id')->nullable()->index();
            $table->foreignId('business_unit_id')->nullable()->index();
            $table->decimal('fee', 12)->nullable()->default(0);
            $table->foreignId('fee_id')->nullable();
            $table->decimal('fee_advert', 12)->nullable();
            $table->enum('model', ['sale_fix', 'sale_share'])->nullable();
            $table->decimal('gross_amount', 12)->nullable();
            $table->decimal('amount', 12)->nullable()->default(0);
            $table->decimal('amount_advert', 12)->nullable()->default(0);
            $table->unsignedInteger('cnt_products')->default(0);
            $table->boolean('sale')->nullable()->default(0);
            $table->boolean('reject')->nullable()->default(0);
            $table->foreignId('reestr_id')->nullable()->index();
            $table->enum('status', ['new', 'sale', 'reject'])->nullable();
            $table->integer('status_cnt')->nullable()->default(0);
            $table->dateTime('status_datetime')->nullable();
            $table->dateTime('last_updated')->nullable();
            $table->boolean('wholesale')->default(false)->index()->comment('Заказ оптовый, не оплачиваем');
            $table->date('date')->nullable()->storedAs('cast(`datetime` as date)');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['order_id', 'offer_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
