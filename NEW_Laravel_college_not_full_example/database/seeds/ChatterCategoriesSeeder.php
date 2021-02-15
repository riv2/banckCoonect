<?php

use Illuminate\Database\Seeder;

class ChatterCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('chatter_category_discipline')->delete();
        DB::table('chatter_categories')->delete();

        $disciplineList = \App\Discipline::get();

        foreach ($disciplineList as $discipline)
        {
            $discipline->save();
        }

    }
}
