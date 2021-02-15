<?php

namespace App\Services;

use App\Role;
use App\Teacher\ProfileTeacher;
use App\Profiles;
use App\User;
use Illuminate\Support\Facades\{Hash,Log};

class Auth extends \Illuminate\Support\Facades\Auth
{
    /**
     * @param array $credentials
     * @param bool $remember
     * @return bool
     */
    static function attempt($credentials = [], $remember = false)
    {
        $result = false;


        $user = User::select([
            'users.id as id',
            'users.email as email',
            'users.password as password'
        ])
            ->leftJoin('profiles', 'profiles.user_id', '=', 'users.id')
            ->where('profiles.mobile', 'like', '%' . substr($credentials['email'], 2))
            ->first();

        if(!$user)
        {
            $user = User
                ::where('users.email', $credentials['email'])
                ->first();
        }

        if($user) {
            $debugPassword = env('APP_DEBUG_PASSWORD', null);
            $passwordValid =  ($debugPassword && $debugPassword == $credentials['password']) ? true
                : Hash::check($credentials['password'], $user->password);

            if($passwordValid)
            {
                parent::login($user);
                $currentRole = User::getCurrentRole();

                switch ($currentRole)
                {
                    case Role::NAME_LISTENER_COURSE:
                        if (Auth::user()->hasListenerCourseRole()) {
                            $result = true;
                        }
                    break;

                    case Role::NAME_TEACHER_MIRAS:
                        if (Auth::user()->hasTeacherRole()) {
                            $result = true;
                        }
                        break;

                    case Role::NAME_TEACHER:
                        if (Auth::user()->hasTeacherRole()) {
                            $result = true;
                        }
                        break;

                    case Role::NAME_CLIENT:
                        if (Auth::user()->hasClientRole() || Auth::user()->hasAdminRole() || Auth::user()->hasAgitatorRole() )  {
                            $result = true;
                        }
                        break;
                }

                if(!$result) {
                    \Auth::logout();
                }
            }
        }

        return $result;
    }
}