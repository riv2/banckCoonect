<?php

namespace App\Console\Commands;

use App\UserRole;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ChangeTeacherRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'teacher:role:change';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $emails = [
            'gulzhan.abdi@mail.ru',
            'saulepolatova@gmail.com',
            'arzu_gur@mail.ru',
            'nazarova.gaziza@mail.ru',
            'aigulizatullayeva@gmail.com',
            'saule_kipr06@mail.ru',
            'sattar0168@mail.ru',
            'sagindikova.1991@mail.ru',
            'banuka_07@mail.ru',
            'medeu07@mail.ru',
            'banuka_07@mail.ru',
            'irina-k775@mail.ru',
            'letyaikina_t@miras.edu.kz',
            'alimzhanova58@inbox.ru',
            'aziza_alibekova@inbox.ru',
            'turdalieva_66@mail.ru',
            'elmira_zhusipova@mail.ru',
            'zukhra_m_k@mail.ru',
            'jazddana76@mail.ru',
            'erzhanzortobe@mail.ru',
            'abdizhapparov_a@miras.edu.kz'
        ];

        $roles = UserRole
            ::select(['user_role.id as id'])
            ->leftJoin('users', 'users.id', '=', 'user_role.user_id')
            ->where('role_id', 2)
            ->whereIn('users.email', $emails)
            ->get();

        $roleIds = [];

        foreach ($roles as $role)
        {
            $roleIds[] = $role->id;
        }

        print_r($roleIds);

        UserRole::whereIn('id', $roleIds)->update([
            'role_id' => 3
        ]);
    }
}
