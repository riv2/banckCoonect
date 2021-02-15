<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMgApplicationConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mg_application_config', function (Blueprint $table) {
            $table->increments('id');

            $table->date('deadline_residence_registration')->nullable()->comment('Важен день и месяц. Год игнорирутеся');
            $table->date('deadline_r086')->nullable()->comment('Важен день и месяц. Год игнорирутеся');
            $table->date('deadline_r063')->nullable()->comment('Важен день и месяц. Год игнорирутеся');
            $table->date('deadline_ent')->nullable()->comment('Важен день и месяц. Год игнорирутеся');
            $table->date('deadline_diploma_supplement')->nullable()->comment('Важен день и месяц. Год игнорирутеся');
            $table->date('deadline_nostrification')->nullable()->comment('Важен день и месяц. Год игнорирутеся');
            $table->date('deadline_military_commision')->nullable()->comment('Важен день и месяц. Год игнорирутеся');
            $table->date('deadline_english_certificate')->nullable()->comment('Важен день и месяц. Год игнорирутеся');
            $table->date('deadline_isbn')->nullable()->comment('Важен день и месяц. Год игнорирутеся');
            $table->date('deadline_work_book')->nullable()->comment('Важен день и месяц. Год игнорирутеся');

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
        Schema::dropIfExists('mg_application_config');
    }
}
