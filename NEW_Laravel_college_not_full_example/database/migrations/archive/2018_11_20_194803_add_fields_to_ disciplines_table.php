<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToDisciplinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE disciplines CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        Schema::table('disciplines', function (Blueprint $table) {
            $table->string('module_number')->nullable();
            $table->string('module_name_ru')->nullable();
            $table->string('module_name_kz')->nullable();
            $table->string('module_name_en')->nullable();

            $table->string('name_kz')->nullable();
            $table->string('name_en')->nullable();

            $table->string('num_ru')->nullable()->comment('шифт на русском');
            $table->string('num_kz')->nullable()->comment('шифт на казахском');
            $table->string('num_en')->nullable()->comment('шифт на англ');

            $table->string('dependence')->nullable()->comment('пререквезиты через запятую');

            $table->string('discipline_cicle')->nullable();

            $table->string('mt_tk')->nullable();
            $table->integer('ects')->nullable()->comment('кредиты европейские');
            $table->text('description')->nullable();
            $table->string('lang')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('disciplines', function (Blueprint $table) {
            $table->dropColumn('module_number');

            $table->dropColumn('module_name_ru');
            $table->dropColumn('module_name_kz');
            $table->dropColumn('module_name_en');
            $table->dropColumn('name_kz');
            $table->dropColumn('name_en');
            $table->dropColumn('num_ru');
            $table->dropColumn('num_kz');
            $table->dropColumn('num_en');
            $table->dropColumn('dependence');
            $table->dropColumn('discipline_cicle');
            $table->dropColumn('mt_tk');
            $table->dropColumn('ects');
            $table->dropColumn('description');
            $table->dropColumn('lang');
        });
    }
}
