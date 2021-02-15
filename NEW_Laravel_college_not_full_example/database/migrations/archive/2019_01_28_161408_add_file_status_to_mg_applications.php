<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFileStatusToMgApplications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mg_applications', function (Blueprint $table) {
            $table->enum('residence_registration_status', ['moderation', 'allow', 'disallow'])->default('moderation')->after('residence_registration_photo');
            $table->enum('military_status', ['moderation', 'allow', 'disallow'])->default('moderation')->after('military_photo');
            $table->enum('r086_status', ['moderation', 'allow', 'disallow'])->default('moderation')->after('r086_photo');
            $table->enum('r063_status', ['moderation', 'allow', 'disallow'])->default('moderation')->after('r063_photo');
            $table->enum('eng_certificate_status', ['moderation', 'allow', 'disallow'])->default('moderation')->after('eng_certificate_photo');
            $table->enum('atteducation_status', ['moderation', 'allow', 'disallow'])->default('moderation')->after('atteducation_photo');
            $table->enum('nostrification_status', ['moderation', 'allow', 'disallow'])->default('moderation')->after('nostrificationattach_photo');
            $table->enum('work_book_status', ['moderation', 'allow', 'disallow'])->default('moderation')->after('work_book_photo');
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
            $table->dropColumn('residence_registration_status');
            $table->dropColumn('military_status');
            $table->dropColumn('r086_status');
            $table->dropColumn('r063_status');
            $table->dropColumn('eng_certificate_status');
            $table->dropColumn('atteducation_status');
            $table->dropColumn('nostrification_status');
            $table->dropColumn('work_book_status');
        });
    }
}
