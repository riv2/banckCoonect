<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersPayMethodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_pay_methods', function (Blueprint $table) {
            $table->id('id');
            $table->foreignId('user_id');
            $table->foreignId('pay_method_id')->nullable();
            $table->string('cc_type', 50)->nullable();
            $table->string('cc_number', 50)->nullable();
            $table->string('company_name', 512)->nullable()->comment('Наименование');
            $table->string('company_inn', 15)->nullable()->comment('ИНН');
            $table->string('bank_company_account')->nullable()->comment('Номер расчетного счета');
            $table->string('bank_identifier_code', 10)->nullable()->comment('БИК');
            $table->string('bank_beneficiary', 512)->nullable()->comment('Название банка');
            $table->string('bank_correspondent_account', 50)->nullable()->comment('Корр. счет');
            $table->string('webmoney_number', 50)->nullable()->comment('Номер WebMoney кошелька');
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
        Schema::dropIfExists('users_pay_methods');
    }
}
