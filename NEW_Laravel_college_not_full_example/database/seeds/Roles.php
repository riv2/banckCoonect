<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Class Roles
 *
 * @property int id
 * @property string name
 * @property string title_ru
 * @property string description
 * @property bool can_set_pay_in_orcabinet
 * @property bool can_upload_student_docs
 * @property bool can_create_student_comment
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class Roles extends Seeder
{
    /**
     * @var array
     */
    private $roleList = [
        [
            'name'      => 'guest',
            'title_ru'  => 'Гость'
        ],
        [
            'name'      => 'admin',
            'title_ru'  => 'Администратор'
        ],
        [
            'name'      => 'client',
            'title_ru'  => 'Студент'
        ],
        [
            'name'      => 'teacher',
            'title_ru'  => 'Преподаватель'
        ],
        [
            'name'      => 'admin_teacher',
            'title_ru'  => 'Админ силлабуса'
        ]
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->roleList as $item)
        {
            $role = \App\Role::where('name', $item['name'])->first();

            if(!$role)
            {
                $role = new \App\Role();
            }

            $role->name = $item['name'];
            $role->title_ru = $item['title_ru'];
            $role->save();
        }
    }
}
