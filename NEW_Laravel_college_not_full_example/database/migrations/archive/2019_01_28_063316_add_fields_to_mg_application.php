<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToMgApplication extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mg_applications', function (Blueprint $table) {
            $table->string('education', 64)->nullable();
            $table->string('bceducation', 30)->nullable();
            $table->string('numeducation', 32)->nullable();
            $table->string('sereducation', 64)->nullable();
            $table->string('nameeducation', 64)->nullable();
            $table->date('dateeducation')->nullable();
            $table->string('cityeducation', 32)->nullable();
            $table->string('atteducation_photo')->nullable();
            $table->boolean('kzornot');
            $table->string('eduspecialty', 64)->nullable();
            $table->string('typevocational', 64)->nullable();
            $table->string('edudegree', 64)->nullable();
            $table->string('eduspecialization', 64)->nullable();
            $table->string('nostrification', 64)->nullable();
            $table->string('nostrificationattach_photo', 64)->nullable();
            $table->string('work_book_photo')->nullable();
            $table->string('eng_certificate_number')->nullable();
            $table->string('eng_certificate_series')->nullable();
            $table->string('eng_certificate_date')->nullable();
            $table->string('eng_certificate_photo')->nullable();

            $table->string('residence_registration_photo')->nullable()->change();
            $table->string('military_photo')->nullable()->change();
            $table->string('r086_photo')->nullable()->change();
            $table->string('r063_photo')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mg_applications', function (Blueprint $table) {
            $table->dropColumn('education');
            $table->dropColumn('bceducation');
            $table->dropColumn('numeducation');
            $table->dropColumn('sereducation');
            $table->dropColumn('nameeducation');
            $table->dropColumn('dateeducation');
            $table->dropColumn('cityeducation');
            $table->dropColumn('atteducation_photo');
            $table->dropColumn('kzornot');
            $table->dropColumn('eduspecialty');
            $table->dropColumn('typevocational');
            $table->dropColumn('edudegree');
            $table->dropColumn('eduspecialization');
            $table->dropColumn('nostrification');
            $table->dropColumn('nostrificationattach_photo');
            $table->dropColumn('work_book_photo');
            $table->dropColumn('eng_certificate_number');
            $table->dropColumn('eng_certificate_series');
            $table->dropColumn('eng_certificate_date');
            $table->dropColumn('eng_certificate_photo');
        });
    }
}
