<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveValidTillFieldFromDiscountStudentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('discount_student', function (Blueprint $table) {
            $table->dropColumn('valid_till');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('discount_student', function (Blueprint $table) {
            $table->timestamp('valid_till')->nullable()->after('status');
        });
    }
}
