<?php

namespace App\Console\Commands;

use App\Profiles;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ClearPhonesDublicate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'phones:dublicate:clear {--check=true}';

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
        $check = $this->option('check') == 'true' ? true : false;

        if($check)
        {
            $this->warn('Check mode!');
        }

        $updateList = [];

        $doubleList = DB::select(DB::raw("
                select p1.id, p1.user_id, p1.mobile, p1.iin, p1.fio, u.email
                from profiles p1
                         left join profiles p2 on p1.mobile = p2.mobile and p1.user_id != p2.user_id
                         left join users u on u.id = p1.user_id
                         left join users u2 on u2.id = p2.user_id
                where
                    p2.id is not null
                  and u.deleted_at is null
                  and u2.deleted_at is null
                  and p1.mobile != ''
                  and p1.iin != p2.iin"));

        foreach ($doubleList as $row)
        {
            $updateList[] = $row->id;
        }

        $updateList = array_unique($updateList);

        print_r($updateList);

        if(!$check)
        {
            Profiles::whereIn('id', $updateList)->update(['mobile' => null]);
        }

        $this->info('Double profiles count: ' . count($updateList));
    }
}
