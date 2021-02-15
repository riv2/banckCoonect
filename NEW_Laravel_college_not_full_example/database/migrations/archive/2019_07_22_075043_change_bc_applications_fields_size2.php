<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeBcApplicationsFieldsSize2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE bc_applications MODIFY COLUMN building_number VARCHAR(255)");
        DB::statement("ALTER TABLE bc_applications MODIFY COLUMN apartment_number VARCHAR(255)");
        DB::statement("ALTER TABLE bc_applications MODIFY COLUMN education VARCHAR(255)");
        DB::statement("ALTER TABLE bc_applications MODIFY COLUMN bceducation VARCHAR(255)");
        DB::statement("ALTER TABLE bc_applications MODIFY COLUMN cityeducation VARCHAR(255)");
        DB::statement("ALTER TABLE bc_applications MODIFY COLUMN typevocational VARCHAR(255)");
        DB::statement("ALTER TABLE bc_applications MODIFY COLUMN edudegree VARCHAR(255)");
        DB::statement("ALTER TABLE bc_applications MODIFY COLUMN nostrification VARCHAR(255)");

        DB::statement("ALTER TABLE mg_applications MODIFY COLUMN building_number VARCHAR(255)");
        DB::statement("ALTER TABLE mg_applications MODIFY COLUMN apartment_number VARCHAR(255)");
        DB::statement("ALTER TABLE mg_applications MODIFY COLUMN education VARCHAR(255)");
        DB::statement("ALTER TABLE mg_applications MODIFY COLUMN bceducation VARCHAR(255)");
        DB::statement("ALTER TABLE mg_applications MODIFY COLUMN cityeducation VARCHAR(255)");
        DB::statement("ALTER TABLE mg_applications MODIFY COLUMN typevocational VARCHAR(255)");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
