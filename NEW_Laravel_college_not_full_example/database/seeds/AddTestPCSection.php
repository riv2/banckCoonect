<?php

use Illuminate\Database\Seeder;

class AddTestPCSection extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $section = new \App\ProjectSection();
        $section->url = 'test_pc_vi';
        $section->name_ru = 'ВИ';
        $section->project = 'admin';
        $section->save();

        $section1 = new \App\ProjectSection();
        $section1->url = 'test_pc_pl';
        $section1->name_ru = 'ПЛ';
        $section1->project = 'admin';
        $section1->save();

        $section2 = new \App\ProjectSection();
        $section2->url = 'test_pc_stud';
        $section2->name_ru = 'ПЛ: студенты';
        $section2->project = 'admin';
        $section2->save();
    }
}
