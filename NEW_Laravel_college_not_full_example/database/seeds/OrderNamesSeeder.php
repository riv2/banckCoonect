<?php

use Illuminate\Database\Seeder;

class OrderNamesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $nameList = [
            [
                'name' => 'Приказ на зачисление',
                'code' => 'ЗЧ'
            ],
            [
                'name' => 'Приказ на перевод из другого ВУЗа',
                'code' => 'ПВ'
            ],
            [
                'name' => 'Приказ на отчисление',
                'code' => 'ОТ'
            ],
            [
                'name' => 'Приказ на выпуск',
                'code' => 'ВП'
            ],
            [
                'name' => 'Приказ на выпуск с отличием',
                'code' => 'ВПО'
            ],
            [
                'name' => 'Приказ на предоставление академ отпуска',
                'code' => 'АК'
            ],
            [
                'name' => 'Приказ на восстановление',
                'code' => 'ВС'
            ],
            [
                'name' => 'Приказ на восстановление из академ отпуска',
                'code' => 'АК'
            ],
            [
                'name' => 'Приказ на перевод на следующий курс',
                'code' => 'ПВ'
            ],
            [
                'name' => 'Приказ на перевод внутри университета',
                'code' => 'ПА'
            ],
            [
                'name' => 'Приказ на распределение по специализациям',
                'code' => 'СП'
            ],
            [
                'name' => 'Приказ на восстановление на ИГА',
                'code' => 'ВСГ'
            ]
        ];

        foreach ($nameList as $k => $val)
        {
            $nameList[$k]['created_at'] = DB::raw('NOW()');
        }

        \App\OrderName::insert($nameList);
    }
}
