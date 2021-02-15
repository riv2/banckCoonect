<?php

use Illuminate\Database\Seeder;

class AddRoleSectionExportSROCourses extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('project_section')->insert([
            'url'          => 'export_sro_courses',
            'name_ru'      => 'Экспорт СРО курсовые',
            'project'      => 'admin',
            'created_at'   => DB::raw('now()'),
            'updated_at'   => DB::raw('now()'),
        ]);
    }
}
