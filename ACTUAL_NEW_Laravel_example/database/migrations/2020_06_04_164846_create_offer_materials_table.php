<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfferMaterialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offer_materials', function (Blueprint $table) {
            $table->increments('offer_material_id');
            $table->integer('offer_id')->index('offer_id');
            $table->string('name')->nullable();
            $table->string('material_type')->index();
            $table->boolean('status')->default(1);
            $table->timestamps();
            $table->softDeletes();
            $table->json('material_params')->nullable()->comment('Параметры и настройки');
            $table->json('material_files')->nullable()->comment('Прилагаемые файлы');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('offer_materials');
    }
}
