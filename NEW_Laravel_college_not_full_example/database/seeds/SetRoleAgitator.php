<?php

use Illuminate\Database\Seeder;

class SetRoleAgitator extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            'name'          => 'agitator',
            'title_ru'      => 'Агитатор',
            'created_at'    => DB::raw('now()'),
            'updated_at'    => DB::raw('now()'),
        ]);
    }
}
