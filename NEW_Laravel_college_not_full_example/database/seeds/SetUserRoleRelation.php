<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Role;

class SetUserRoleRelation extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userList = User::select([
            'id',
            DB::raw('lower(usertype) as usertype')
        ])->get();

        $roleAdmin      = Role::select(['id'])->where('name', Role::NAME_ADMIN)->first();
        $roleClient     = Role::select(['id'])->where('name', Role::NAME_CLIENT)->first();
        $roleTeacher    = Role::select(['id'])->where('name', Role::NAME_TEACHER)->first();

        if(!$roleAdmin || !$roleClient || !$roleTeacher)
        {
            echo 'Roles not found';
            return;
        }

        DB::table('user_role')->truncate();
        DB::table('user_role')->insert($this->combineUserRoleArray(
            $userList,
            $roleAdmin->id,
            $roleClient->id,
            $roleTeacher->id
        ));
    }

    /**
     * @param $userList
     * @param $roleAdminId
     * @param $roleClientId
     * @param $roleTeacherId
     * @return array
     */
    public function combineUserRoleArray($userList, $roleAdminId, $roleClientId, $roleTeacherId)
    {
        $userRoleList = [];

        foreach ($userList as $user)
        {
            $roleId = $roleClientId;

            switch ($user->usertype) {
                case 'admin':
                    $roleId = $roleAdminId;
                    break;

                case 'client':
                    $roleId = $roleClientId;
                    break;

                case 'teacher':
                    $roleId = $roleTeacherId;
                    break;
            }

            $userRoleList[] = [
                'user_id'       => $user->id,
                'role_id'       => $roleId,
                'created_at'    => DB::raw('now()'),
                'updated_at'    => DB::raw('now()')
            ];
        }

        return $userRoleList;
    }
}
