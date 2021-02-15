<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableAuditFinanceNomenclatures extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audit_finance_nomenclatures', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->string('user_name',255)->nullable(true);
            $table->unsignedInteger('owner_id');
            $table->string('owner_name',255)->nullable(true);
            $table->unsignedInteger('service_id');
            $table->string('service_name',255)->nullable(true);
            $table->string('service_code',255)->nullable(true);
            $table->integer('cost');
            $table->tinyInteger('count')->nullable(true);
            $table->enum('status', [
                'process',
                'fail',
                'success'
            ])->default('fail');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('owner_id')->references('id')->on('users');
            $table->foreign('service_id')->references('id')->on('finance_nomenclatures');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('audit_finance_nomenclatures');
    }
}
