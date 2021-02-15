<?php

use Illuminate\Database\Seeder;

class SetEntDisciplineList extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        \DB::table('ent_discipline_list')->insert([
            0 => [
                'id'     => 1,
                'name'   => 'Биология'
            ],
            1 => [
                'id'     => 2,
                'name'   => 'География'
            ],
            2 => [
                'id'     => 3,
                'name'   => 'Химия'
            ],
            3 => [
                'id'     => 4,
                'name'   => 'Математика'
            ],
            4 => [
                'id'     => 5,
                'name'   => 'Физика'
            ],
            5 => [
                'id'     => 6,
                'name'   => 'Казахский язык'
            ],
            6 => [
                'id'     => 7,
                'name'   => 'Казахская литература'
            ],
            7 => [
                'id'     => 8,
                'name'   => 'Иностранный язык'
            ],
            8 => [
                'id'     => 9,
                'name'   => 'Всемирная история'
            ],
            9 => [
                'id'     => 10,
                'name'   => 'Человек. Общество. Право'
            ],
            10 => [
                'id'     => 11,
                'name'   => 'Физика'
            ],
            11 => [
                'id'     => 12,
                'name'   => 'Творческий экзамен 1'
            ],
            12 => [
                'id'     => 13,
                'name'   => 'Творческий экзамен 2'
            ]
        ]);

    }
}
