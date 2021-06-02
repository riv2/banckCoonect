<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pp', function (Blueprint $table) {
            $table->id('id');
            $table->foreignId('user_id')->nullable()->index();
            $table->string('tech_domain')->nullable()->index();
            $table->string('prod_domain')->nullable()->index();
            $table->string('short_name')->nullable();
            $table->string('long_name')->nullable();
            $table->string('onboarding_status')->nullable();
            $table->string('company_url')->nullable();
            $table->enum('pp_target', ['lead', 'products'])->nullable();
            $table->enum('currency', ['RUB', 'EUR', 'USD'])->nullable();
            $table->string('logo')->nullable();
            $table->string('branch')->nullable();
            $table->string('color1')->nullable();
            $table->string('color2')->nullable();
            $table->string('color3')->nullable();
            $table->string('color4')->nullable();
            $table->string('lang')->default('ru');
            $table->enum('tariff', ['free', 'start', 'professional'])->default('free');
            $table->enum('status', ['active', 'banned', 'stopped'])->default('active');
            $table->dateTime('demo_ends_at')->nullable();
            $table->text('comment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pp');
    }
}
