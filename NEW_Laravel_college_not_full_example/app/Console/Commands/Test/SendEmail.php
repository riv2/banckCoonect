<?php

namespace App\Console\Commands\Test;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:email {--count=0} {--index=null}';

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
        Log::useDailyFiles(storage_path('logs/test_send_email_' . date('Y_m_d', time()) . '.log'));

        $index = $this->option('index');
        $count = $this->option('count');

        if($index == 'null')
        {
            $this->runList($count);
        }
        else
        {
            $this->runSingle($index);
        }
    }

    public function runList($count)
    {
        for ($i = 1; $i <= $count; $i++)
        {
            exec('php7.2 artisan send:email --index=' . $i . ' > /dev/null 2>/dev/null &');
        }
    }

    /**
     * @param $index
     */
    public function runSingle($index)
    {
        sleep(rand(1, 10));

        $emailList = [
            'vbondarenko89@gmail.com',
            'dadiccvv@mail.ru',
            'dadiccvv@gmail.com',
            'viktor.shepkin@gmail.com',
            'gnurlan@gmail.com',
            'dalbich@yandex.ru',
            'n@studyon.cz',
            'rhastaman@gmail.com',
            'murtazin@yahoo.com',
            'p1ay3r.art@gmail.com'
        ];
        $userName = '';

        foreach ($emailList as $userEmail)
        {
            Mail::send('emails.register_confirm',
                array(
                    'email' => $userEmail,
                    'password' => '123456',
                    'confirmation_code' => str_random(20),
                    'user_message' => 'test'
                ), function ($message) use ($userName, $userEmail) {
                    $message->from(getcong('site_email'), getcong('site_name'));
                    $message->to($userEmail, $userName)->subject('Registration Confirmation');
                });
        }
    }
}
