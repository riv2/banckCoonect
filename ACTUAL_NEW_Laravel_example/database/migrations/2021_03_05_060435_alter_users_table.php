<?php

use App\Helpers\ExtendedMigration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUsersTable extends ExtendedMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['manager', 'admin', 'advertiser', 'partner', 'analyst'])
                ->default('partner')
                ->after('email')
                ->change();
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
            $table->enum('role', ['manager', 'admin', 'advertiser', 'partner'])
                ->default('partner')
                ->after('email')->change();;
        });
    }
}
