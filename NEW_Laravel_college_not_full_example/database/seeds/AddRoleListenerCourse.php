<?php

use Illuminate\Database\Seeder;

class AddRoleListenerCourse extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            'name'          => 'listener_course',
            'title_ru'      => 'Слушатель курсов',
            'created_at'    => DB::raw('now()'),
            'updated_at'    => DB::raw('now()'),
        ]);
    }
}
