<?php

use Illuminate\Database\Seeder;

class AddRoleGuestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = new \App\Role();
        $role->name = 'guest';
        $role->title_ru = 'Гость';
        $role->save();
    }
}
