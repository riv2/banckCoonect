<?php

use Illuminate\Database\Seeder;

class LanguagesLevelInit extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('languages_level')->insert([
            0 => [
                'id'   => 1,
                'name' => 'Начальный'
            ],
            1 => [
                'id'   => 2,
                'name' => 'Элементарный'
            ],
            2 => [
                'id'   => 3,
                'name' => 'Средний'
            ],
            3 => [
                'id'   => 4,
                'name' => 'Средне-продвинутый'
            ],
            4 => [
                'id'   => 5,
                'name' => 'Продвинутый'
            ],
            5 => [
                'id'   => 6,
                'name' => 'В совершенстве'
            ],
        ]);

    }
}
