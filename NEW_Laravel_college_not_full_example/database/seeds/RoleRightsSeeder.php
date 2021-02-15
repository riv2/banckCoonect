<?php

use Illuminate\Database\Seeder;

class RoleRightsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = \App\Role::where('name', 'admin_teacher')->first();

        if(!$role)
        {
            return;
        }

        $sectionDiscipline = \App\ProjectSection::where('url', 'disciplines')->first();
        $sectionThemes = \App\ProjectSection::where('url', 'themes')->first();

        $roleRightList[] = [
            'role_id'               => $role->id,
            'project_section_id'    => $sectionDiscipline->id,
            'can_read'              => true,
            'can_create'            => false,
            'can_edit'              => false,
            'can_delete'            => false,
            'created_at'            => DB::raw('now()'),
            'updated_at'            => DB::raw('now()')
        ];

        $roleRightList[] = [
            'role_id'               => $role->id,
            'project_section_id'    => $sectionThemes->id,
            'can_read'    => true,
            'can_create'    => true,
            'can_edit'    => true,
            'can_delete'    => true,
            'created_at'            => DB::raw('now()'),
            'updated_at'            => DB::raw('now()')
        ];

        DB::table('role_rights')->truncate();
        DB::table('role_rights')->insert($roleRightList);
    }
}
