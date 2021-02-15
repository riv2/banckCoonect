<?php

namespace App\Console\Commands\Test;

use App\User;
use App\UserRole;
use Faker\Factory;
use Illuminate\Console\Command;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:user:create {--count=0}';

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
        $count = $this->option('count');

        if(!$count)
        {
            $this->error('Count not found');
            return;
        }

        $email = 'auto_test_#i@mail.ru';
        $userList = [];

        for($i = 1; $i <= $count; $i++)
        {
            $emailUser = str_replace('#i', $i, $email);

            $user = new User([
                'email'     => $emailUser,
                'name'      => 'Тест',
                'password'  => bcrypt('123123'),
                'status'    => 1
            ]);

            $user->save();

            UserRole::insert([
                'user_id' => $user->id,
                'role_id' => 2
            ]);
        }

        User::insert($userList);
    }
}
