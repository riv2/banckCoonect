<?php

use App\DisciplineSubmodule;
use Illuminate\Database\Seeder;

class DisciplinesSubmodulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DisciplineSubmodule::where('id', '>', 0)->delete();

        $data = [
            [
                'discipline_id' => 2549,
                'submodule_id' => 1
            ],
            [
                'discipline_id' => 2550,
                'submodule_id' => 1
            ],
            [
                'discipline_id' => 897,
                'submodule_id' => 1
            ],
            [
                'discipline_id' => 898,
                'submodule_id' => 1
            ],

            [
                'discipline_id' => 2550,
                'submodule_id' => 2
            ],
            [
                'discipline_id' => 897,
                'submodule_id' => 2
            ],
            [
                'discipline_id' => 898,
                'submodule_id' => 2
            ],
            [
                'discipline_id' => 899,
                'submodule_id' => 2
            ],

            [
                'discipline_id' => 901,
                'submodule_id' => 3
            ],
            [
                'discipline_id' => 902,
                'submodule_id' => 3
            ],
            [
                'discipline_id' => 903,
                'submodule_id' => 3
            ],
            [
                'discipline_id' => 904,
                'submodule_id' => 3
            ],
//            [
//                'discipline_id' => 905,
//                'submodule_id' => 3
//            ],

            [
                'discipline_id' => 2721,
                'submodule_id' => 4
            ],
            [
                'discipline_id' => 2722,
                'submodule_id' => 4
            ],
            [
                'discipline_id' => 2723,
                'submodule_id' => 4
            ],
            [
                'discipline_id' => 2724,
                'submodule_id' => 4
            ]
        ];

        foreach ($data as $datum) {
            $new = new DisciplineSubmodule();
            $new->discipline_id = $datum['discipline_id'];
            $new->submodule_id = $datum['submodule_id'];
            $new->save();
        }
    }
}
