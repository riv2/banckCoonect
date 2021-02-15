<?php

use Illuminate\Database\Seeder;

class ChatterClearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('chatter_category_discipline')->delete();
        DB::table('chatter_post')->delete();
        DB::table('chatter_discussion')->delete();
        DB::table('chatter_categories')->delete();
    }
}
