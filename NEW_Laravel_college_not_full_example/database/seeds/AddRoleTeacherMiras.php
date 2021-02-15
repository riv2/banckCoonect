<?php

use Illuminate\Database\Seeder;

class AddRoleTeacherMiras extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $role = new \App\Role();
        $role->name = 'teacher_miras';
        $role->title_ru = 'Преподаватель Мирас';
        $role->save();
    }
}
