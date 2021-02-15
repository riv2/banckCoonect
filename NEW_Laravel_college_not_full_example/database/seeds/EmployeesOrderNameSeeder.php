<?php

use Illuminate\Database\Seeder;

class EmployeesOrderNameSeeder extends Seeder
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
                'code' => 'business_trip', 
                'name' => 'О командировании',
                'new_status' => 'сотрудник'
            ],
            [
                'code' => 'recruitment', 
                'name' => 'О приёме на работу',
                'new_status' => 'сотрудник'
            ],
            [
                'code' => 'work_leave', 
                'name' => 'О трудовом отпуске',
                'new_status' => 'сотрудник'
            ],
            [
                'code' => 'bid_change', 
                'name' => 'Об изменении ставки',
                'new_status' => 'сотрудник'
            ],
            [
                'code' => 'salary_change', 
                'name' => 'Об изменении оклада',
                'new_status' => 'сотрудник'
            ],
            [
                'code' => 'combination_of_posts', 
                'name' => 'О совмещении должностей',
                'new_status' => 'сотрудник'
            ],
            [
                'code' => 'recall_from_work_leave', 
                'name' => 'Об отзыве из трудового отпуска',
                'new_status' => 'сотрудник'
            ],
            [
                'code' => 'financial_allocation', 
                'name' => 'О выделении финансовых средств',
                'new_status' => 'сотрудник'
            ],
            [
                'code' => 'abolition_of_combining_posts', 
                'name' => 'Об отмене совмещения должностей',
                'new_status' => 'сотрудник'
            ],
            [
                'code' => 'termination_of_an_employment_contract', 
                'name' => 'О расторжении трудового договора',
                'new_status' => 'уволен'
            ],
            [
                'code' => 'transfer_from_post_to_post', 
                'name' => 'О переводе с должности на должность',
                'new_status' => 'сотрудник'
            ],
            [
                'code' => 'leave_without_pay', 
                'name' => 'Об отпуске без сохранение зар. платы',
                'new_status' => 'сотрудник'
            ],
            [
                'code' => 'extension_of_maternity_leave', 
                'name' => 'О продлении отпуска по беременности и родам',
                'new_status' => 'декретный отпуск'
            ],
            [
                'code' => 'maternity_leave', 
                'name' => 'О предоставлении отпуска по беременности и родам',
                'new_status' => 'декретный отпуск'
            ],
            [
                'code' => 'leave_without_preservation_of_wages_for_childcare', 
                'name' => 'Отпуск без сохранения зароботной платы по уходу за ребёнком',
                'new_status' => 'декретный отпуск'
            ]
        ];

        \App\EmployeesOrderName::truncate();
        
        foreach ($nameList as $val)
        {
            \App\EmployeesOrderName::create($val);
        }
    }
}
