<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['manager', 'admin', 'advertiser', 'partner'])->default('partner')->after('email');
            $table->foreignId('pp_id')->nullable()->default(null)->after('role');
            $table->integer('status')->default(0)->after('password');
            $table->integer('need_api')->default(0)->after('status');
            $table->string('comment')->nullable()->after('need_api');
            $table->string('phone')->nullable()->after('comment');
            $table->string('skype')->nullable()->after('phone');
            $table->integer('all_docs')->default(0)->after('skype');
            $table->integer('all_fields')->default(0)->after('all_docs');
            $table->integer('balance_popup')->default(0)->after('all_fields');
            $table->integer('email_unsubs')->default(0)->after('balance_popup');
            $table->enum('model', ['approve','sale','lead','activation','transaction'])->default('sale')->after('email_unsubs');
            $table->string('auth_token', 100)->nullable()->after('remember_token');

            $table->dropIndex('users_email_unique');

            $table->unique(['email', 'pp_id'], 'emailpp');
            $table->unique(['auth_token'], 'users_auth_token_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
            $table->dropColumn('status');
            $table->dropColumn('pay_method');
            $table->dropColumn('need_api');
            $table->dropColumn('comment');
            $table->dropColumn('phone');
            $table->dropColumn('skype');
            $table->dropColumn('pay_account');
            $table->dropColumn('all_docs');
            $table->dropColumn('all_fields');
            $table->dropColumn('balance_popup');
            $table->dropColumn('email_unsubs');
            $table->dropColumn('model');
        });
    }
}
