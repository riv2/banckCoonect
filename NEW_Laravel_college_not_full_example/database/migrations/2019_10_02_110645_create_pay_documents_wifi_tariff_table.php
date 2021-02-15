<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePayDocumentsWifiTariffTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('pay_documents_wifi_tariff')) {
            Schema::create('pay_documents_wifi_tariff', function (Blueprint $table) {
                $table->unsignedInteger('pay_document_id');
                $table->foreign('pay_document_id')->references('id')->on('pay_documents');

                $table->unsignedInteger('wifi_tariff_id');
                $table->foreign('wifi_tariff_id')->references('id')->on('wifi_tariff');

                $table->timestamps();
                $table->softDeletes();

                $table->primary([
                    'pay_document_id',
                    'wifi_tariff_id'
                ]);
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
        Schema::dropIfExists('pay_documents_wifi_tariff');
    }
}
