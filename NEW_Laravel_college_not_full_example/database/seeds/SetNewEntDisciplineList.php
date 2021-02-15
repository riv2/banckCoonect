<?php

use Illuminate\Database\Seeder;

class SetNewEntDisciplineList extends Seeder
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
                'id'     => 14,
                'name'   => 'История Казахстана'
            ],
            1 => [
                'id'     => 15,
                'name'   => 'Грамотность чтения'
            ]
        ]);

    }
}
