<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offers', function (Blueprint $table) {
            $table->id('id');
            $table->foreignId('user_id')->nullable();
            $table->foreignId('pp_id')->nullable();
            $table->string('offer_name')->nullable();
            $table->enum('model', ['new', 'sale'])->nullable();
            $table->enum('fee_type', ['fix', 'share'])->nullable();
            $table->decimal('fee', 12, 4)->default(0.0000);
            $table->decimal('fee_advert', 12, 4)->default(0.0000);
            $table->double('ctr', 8, 2)->nullable();
            $table->double('cr', 8, 2)->nullable();
            $table->double('ar', 8, 2)->nullable();
            $table->string('info_link')->nullable();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->string('utm_source')->nullable();
            $table->string('click_id_name')->nullable();
            $table->string('web_id_name')->nullable();
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
        Schema::dropIfExists('offers');
    }
}
