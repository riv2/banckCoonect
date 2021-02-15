<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeEduspecialitySize extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('bc_applications', function (Blueprint $table) {
            DB::statement("ALTER TABLE bc_applications MODIFY COLUMN eduspecialty TEXT");
            DB::statement("ALTER TABLE mg_applications MODIFY COLUMN eduspecialty TEXT");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bc_applications', function (Blueprint $table) {
            /*DB::statement("ALTER TABLE bc_applications MODIFY COLUMN eduspecialty TEXT");
            DB::statement("ALTER TABLE mg_applications MODIFY COLUMN eduspecialty TEXT");*/
        });
    }
}
