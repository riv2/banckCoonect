<?php

use Illuminate\Database\Seeder;

class AddExportSROCOursesPayedRole extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            'name'          => 'export_sro_corses',
            'title_ru'      => 'Выгрузка СРО курсовые',
            'created_at'    => DB::raw('now()'),
            'updated_at'    => DB::raw('now()'),
        ]);
    }
}
