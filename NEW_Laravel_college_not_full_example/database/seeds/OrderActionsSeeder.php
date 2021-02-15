<?php

use Illuminate\Database\Seeder;

class OrderActionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $actionList = [
            'Отчислить из университета за академическую задолженность',
            'Отчислить из университета за финансовую задолженность',
            'Отчислить из университета за потерю связи',
            'Зачислить на платную форму обучения',
            'Перевести на следующий курс',
            'Допустить к сдаче Государственного экзамена и защите дипломной работы',
            'Предоставить академический отпуск',
            'Восстановить из академического отпуска'
        ];

        $insertList = [];

        foreach ($actionList as $k => $val)
        {
            $insertList[] = [
                'name' =>$val,
                'created_at' => DB::raw('NOW()')
            ];
        }

        \App\OrderAction::insert($insertList);
    }
}
