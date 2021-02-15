<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCancelStatusToPayDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pay_documents', function (Blueprint $table) {
            DB::statement("alter table `pay_documents` modify column `status` enum ('process', 'success', 'fail', 'cancel')");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pay_documents', function (Blueprint $table) {
            DB::statement("alter table `pay_documents` modify column `status` enum ('process', 'success', 'fail')");
        });
    }
}
