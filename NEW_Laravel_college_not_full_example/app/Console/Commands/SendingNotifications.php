<?php

namespace App\Console\Commands;

use App\Profiles;
use App\Services\SmsService;
use App\Services\Translit;
use App\User;
use Illuminate\Console\Command;

class SendingNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:notifications {--phone=null}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send sms or email notifications';

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
        $phone = $this->option('phone');
        $users = User::select(['users.id as id', 'mobile', 'profiles.fio as fio', 'users.email as login'])
            ->leftJoin('profiles', 'profiles.user_id', '=', 'users.id')
            ->where('profiles.category', '!=', Profiles::CATEGORY_TRANSIT)
            ->whereNotNull('profiles.id')
            ->where('alien', 0)
            ->where('profiles.registration_step', 'finish')
            ->where('users.created_at', '<', '2019-01-01');

        if($phone != 'null')
        {
            $users->where('profiles.mobile', $phone);
        }

        $userCount = $users;
        $userCount = $userCount->count();
        $realSendCount = 0;
        $sended = [];

        $this->output->progressStart($userCount);

        $users->chunk(1000, function($users) use($sended, &$realSendCount)
        {
            foreach ($users as $user)
            {
                //$user->login = str_replace('@', ' @ ', $user->login);

                if(!in_array($user->mobile, $sended) && !in_array($user->login, $sended)) {

                    $smsMessage = $this->getShortFio($user->fio) . ' Sizdin login/ Vash login ' . $user->mobile . '. Jana paroli/Novyi parol Miras2019. Oku portal/ Uchebniy portal MIRAS.APP';

                    if(strlen($smsMessage) > 160)
                    {
                        $smsMessage = $this->getShortFio($user->fio) . ' Sizdin login/ Vash login ' . $user->mobile . '. Jana paroli/Novyi parol Miras2019. MIRAS.APP';
                    }

                    if ($user->mobile && SmsService::isKazNumber($user->mobile) && strlen($smsMessage) <= 160) {
                        SmsService::send($user->mobile, $smsMessage);
                        $realSendCount = $realSendCount + 1;
                    }

                    $sended[] = $user->login;
                    $sended[] = $user->mobile;
                }

                $this->output->progressAdvance();
            }
        });

        $this->output->progressFinish();

        $this->info('Real send count: ' . $realSendCount);
    }

    /**
     * @param $fio
     * @return string
     */
    public function getShortFio($fio)
    {
        $fio = Translit::simple($fio);
        $parts = explode(' ', $fio);

        $patr = $parts[2] ?? null;
        $patr = $patr ? substr($patr, 0, 1) . '.' : '';

        return implode(' ', [
            $parts[0] ?? null,
            $parts[1] ?? null,
            $patr ?? null,

        ]);
    }
}
