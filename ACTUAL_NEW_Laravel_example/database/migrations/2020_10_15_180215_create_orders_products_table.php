<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders_products', function (Blueprint $table) {
            $table->id('id');
            $table->string('order_id')->default('');
            $table->timestamp('datetime');
            $table->foreignId('pp_id');
            $table->foreignId('partner_id')->nullable();
            $table->foreignId('category_id')->nullable();
            $table->foreignId('offer_id')->nullable();
            $table->foreignId('link_id')->nullable();
            $table->string('product_id', 32);
            $table->string('product_name')->nullable();
            $table->string('category')->nullable();
            $table->foreignId('business_unit_id')->nullable();
            $table->decimal('price', 12);
            $table->unsignedInteger('quantity')->nullable();
            $table->decimal('total', 12)->nullable();
            $table->decimal('fee', 12)->nullable();
            $table->foreignId('fee_id')->nullable();
            $table->enum('fee_type', ['sale_fix', 'sale_share'])->nullable();
            $table->enum('status', ['new', 'sale', 'approve', 'reject'])->default('new');
            $table->string('web_id')->nullable();
            $table->string('click_id')->nullable();
            $table->foreignId('pixel_id')->nullable();
            $table->foreignId('reestr_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['order_id', 'offer_id', 'product_id', 'price']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders_products');
    }
}
