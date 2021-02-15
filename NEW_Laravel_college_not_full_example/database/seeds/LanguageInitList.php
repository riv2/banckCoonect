<?php

use Illuminate\Database\Seeder;

class LanguageInitList extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('languages')->insert([
            0 => [
                'id'        => 1,
                'code'      => 'ru',
                'name'      => 'Русский',
                'name_en'   => 'Russian',
                'sorting'   => 500
            ],
            1 => [
                'id'        => 2,
                'code'      => 'kk',
                'name'      => 'Казахский',
                'name_en'   => 'Kazakh',
                'sorting'   => 400
            ],
            2 => [
                'id'        => 3,
                'code'      => 'en',
                'name'      => 'Английский',
                'name_en'   => 'English',
                'sorting'   => 300
            ],
            3 => [
                'id'        => 4,
                'code'      => 'ab',
                'name'      => 'Абхазский',
                'name_en'   => 'Abkhazian',
                'sorting'   => 0
            ],
            4 => [
                'id'        => 5,
                'code'      => 'ar',
                'name'      => 'Арабский',
                'name_en'   => 'Arab',
                'sorting'   => 0
            ],
            5 => [
                'id'        => 6,
                'code'      => 'ka',
                'name'      => 'Грузинский',
                'name_en'   => 'Georgian',
                'sorting'   => 0
            ],
            6 => [
                'id'        => 7,
                'code'      => 'es',
                'name'      => 'Испанский',
                'name_en'   => 'Spanish',
                'sorting'   => 0
            ],
            7 => [
                'id'        => 8,
                'code'      => 'it',
                'name'      => 'Итальянский',
                'name_en'   => 'Italian',
                'sorting'   => 0
            ],
            8 => [
                'id'        => 9,
                'code'      => 'de',
                'name'      => 'Немецкий',
                'name_en'   => 'German',
                'sorting'   => 0
            ],
            9 => [
                'id'        => 10,
                'code'      => 'pl',
                'name'      => 'Польский',
                'name_en'   => 'Polish',
                'sorting'   => 0
            ],
            10 => [
                'id'        => 11,
                'code'      => 'tr',
                'name'      => 'Турецкий',
                'name_en'   => 'Turkish',
                'sorting'   => 0
            ],
            11 => [
                'id'        => 12,
                'code'      => 'uz',
                'name'      => 'Узбекский',
                'name_en'   => 'Uzbek',
                'sorting'   => 0
            ],
            12 => [
                'id'        => 13,
                'code'      => 'uk',
                'name'      => 'Украинский',
                'name_en'   => 'Ukrainian',
                'sorting'   => 0
            ],
            13 => [
                'id'        => 14,
                'code'      => 'fr',
                'name'      => 'Французский',
                'name_en'   => 'French',
                'sorting'   => 0
            ],
            14 => [
                'id'        => 15,
                'code'      => 'sv',
                'name'      => 'Шведский',
                'name_en'   => 'Swedish',
                'sorting'   => 0
            ],
            15 => [
                'id'        => 16,
                'code'      => 'et',
                'name'      => 'Эстонский',
                'name_en'   => 'Estonian',
                'sorting'   => 0
            ],
            16 => [
                'id'        => 17,
                'code'      => 'tg',
                'name'      => 'Таджикский',
                'name_en'   => 'Tajik',
                'sorting'   => 0
            ],
            17 => [
                'id'        => 18,
                'code'      => 'pt',
                'name'      => 'Португальский',
                'name_en'   => 'Portuguese',
                'sorting'   => 0
            ],
            18 => [
                'id'        => 19,
                'code'      => 'mn',
                'name'      => 'Монгольский',
                'name_en'   => 'Mongolian',
                'sorting'   => 0
            ],
            19 => [
                'id'        => 20,
                'code'      => 'lv',
                'name'      => 'Латвийский',
                'name_en'   => 'Latvian',
                'sorting'   => 0
            ],
            20 => [
                'id'        => 21,
                'code'      => 'ky',
                'name'      => 'Киргизский',
                'name_en'   => 'Kyrgyz',
                'sorting'   => 0
            ]
        ]);
    }
}
