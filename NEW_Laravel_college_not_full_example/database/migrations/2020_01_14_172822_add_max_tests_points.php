<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddMaxTestsPoints extends Migration
{
    public function up()
    {
        Schema::table(
            'students_disciplines',
            function (Blueprint $table) {
//                $table->unsignedInteger('test1_max_points')->after('test1_qr_checked')->nullable();
//                $table->unsignedInteger('test_max_points')->after('test_qr_checked')->nullable();
//                $table->dropColumn('total_points');
            }
        );
    }

    public function down()
    {
        Schema::table(
            'students_disciplines',
            function (Blueprint $table) {
                $table->decimal('total_points', 8, 2)->after('iteration')->nullable();
                $table->dropColumn('test1_max_points');
                $table->dropColumn('test_max_points');
            }
        );
    }
}