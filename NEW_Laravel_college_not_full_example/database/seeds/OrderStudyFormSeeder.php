<?php

use Illuminate\Database\Seeder;

class OrderStudyFormSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $list = [
            'Заочная и вечерняя',
            'Дистанционная',
            'ФЭПИТ очная бакалавриат',
            'Магистратура',
            'ФПИЯ очная бакалавриат'
        ];

        foreach ($list as $item)
        {
            $model = new \App\OrderStudyForm();
            $model->name = $item;
            $model->save();
        }
    }
}
