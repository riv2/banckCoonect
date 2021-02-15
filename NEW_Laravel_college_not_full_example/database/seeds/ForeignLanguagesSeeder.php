<?php

use Illuminate\Database\Seeder;

class ForeignLanguagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('submodules')->insert([
            [
                'id' => 1,
                'name' => 'Иностранный язык 1',
                'name_kz' => 'Шетел тілі 1',
                'name_en' => 'Foreign Language 1',
                'ects' => 5,
                'dependence' => '',
            ],
            [
                'id' => 2,
                'name' => 'Иностранный язык 2',
                'name_kz' => 'Шетел тілі 2',
                'name_en' => 'Foreign Language 2',
                'ects' => 5,
                'dependence' => '1',
            ],
            [
                'id' => 3,
                'name' => 'Казахский (русский) язык 1',
                'name_kz' => 'Қазақ тілі 1',
                'name_en' => 'Kazakh Language 1',
                'ects' => 5,
                'dependence' => '',
            ],
            [
                'id' => 4,
                'name' => 'Казахский (русский) язык 2',
                'name_kz' => 'Қазақ тілі 2',
                'name_en' => 'Kazakh Language 2',
                'ects' => 5,
                'dependence' => '3',
            ]
        ]);

        DB::table('module_submodule')->insert([
            [
                'id' => 1,
                'submodule_id' => 1,
                'module_id' => 109
            ],
            [
                'id' => 2,
                'submodule_id' => 2,
                'module_id' => 109
            ],
            [
                'id' => 3,
                'submodule_id' => 3,
                'module_id' => 109
            ],
            [
                'id' => 4,
                'submodule_id' => 4,
                'module_id' => 109
            ]
        ]);

        DB::table('discipline_submodule')->insert([
            [
                'id' => 1,
                'discipline_id' => 2549,
                'submodule_id' =>1
            ],
            [
                'id' => 2,
                'discipline_id' => 2550,
                'submodule_id' =>1
            ],
            [
                'id' => 3,
                'discipline_id' => 897,
                'submodule_id' =>1
            ],
            [
                'id' => 4,
                'discipline_id' => 898,
                'submodule_id' =>1
            ],
            [
                'id' => 7,
                'discipline_id' => 2550,
                'submodule_id' =>2
            ],
            [
                'id' => 8,
                'discipline_id' => 897,
                'submodule_id' =>2
            ],
            [
                'id' => 9,
                'discipline_id' => 898,
                'submodule_id' =>2
            ],
            [
                'id' => 10,
                'discipline_id' => 899,
                'submodule_id' =>2
            ],
            [
                'id' => 12,
                'discipline_id' => 901,
                'submodule_id' =>3
            ],
            [
                'id' => 13,
                'discipline_id' => 902 	,
                'submodule_id' =>3
            ],
            [
                'id' => 14,
                'discipline_id' => 903,
                'submodule_id' =>3
            ],
            [
                'id' => 15,
                'discipline_id' => 904,
                'submodule_id' =>3
            ],
            [
                'id' => 16,
                'discipline_id' => 905,
                'submodule_id' =>3
            ],
            [
                'id' => 18,
                'discipline_id' => 902 	,
                'submodule_id' =>4
            ],
            [
                'id' => 19,
                'discipline_id' => 903,
                'submodule_id' =>4
            ],
            [
                'id' => 20,
                'discipline_id' => 904,
                'submodule_id' =>4
            ],
            [
                'id' => 21,
                'discipline_id' => 905,
                'submodule_id' =>4
            ],
            [
                'id' => 22,
                'discipline_id' => 906,
                'submodule_id' =>4
            ]
        ]);

        DB::table('disciplines')->whereIN('id', [901, 2549])->update(['language_level' => 1]);
        DB::table('disciplines')->whereIN('id', [902, 2550])->update(['language_level' => 2]);
        DB::table('disciplines')->whereIN('id', [903, 897])->update(['language_level' => 3]);
        DB::table('disciplines')->whereIN('id', [904, 898])->update(['language_level' => 4]);
        DB::table('disciplines')->whereIN('id', [905, 899])->update(['language_level' => 5]);
        DB::table('disciplines')->whereIN('id', [900, 906])->update(['language_level' => 6]);
    }
}
