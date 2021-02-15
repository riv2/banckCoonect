<?php

use Illuminate\Database\Seeder;

class OrderActions2Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $actionList = [
            'Отчислен из университета по собственному желанию',
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
