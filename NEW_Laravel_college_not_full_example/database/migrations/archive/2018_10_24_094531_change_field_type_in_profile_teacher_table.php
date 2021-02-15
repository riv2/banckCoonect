<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeFieldTypeInProfileTeacherTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('profile_teachers', function (Blueprint $table) {
            $table->dropColumn('sex');
        });

        Schema::table('profile_teachers', function (Blueprint $table) {
            $table->date('bdate')->change();
            $table->date('issuedate')->change();
            $table->enum('doctype', ['pass', 'id'])->after('bdate');
            $table->enum('sex', ['male', 'female']);

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
        Schema::table('profile_teachers', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
            $table->dropColumn('doctype');
        });
    }
}
