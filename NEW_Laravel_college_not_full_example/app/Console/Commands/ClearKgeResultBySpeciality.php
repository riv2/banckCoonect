<?php

namespace App\Console\Commands;

use App\QuizeResultKge;
use App\Speciality;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ClearKgeResultBySpeciality extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:kge:result:bydiscipline {--id=} {--method=check}';

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
        $specialityId = $this->option('id');
        $method = $this->option('method');

        $speciality = Speciality
            ::with(['profiles' => function($query){
                /*$query->select([
                    'users.id as id',
                    'profiles.user_id as user_id',
                    'users.email as email'
                ]);*/
                $query->leftJoin('users', 'users.id', '=', 'profiles.user_id');
                $query->where('users.keycloak', true);
                $query->where('import_type', User::IMPORT_TYPE_GOS_TEST);
            }])
            ->where('id', $specialityId)
            ->first();

        $userIdList = [];

        $this->info($speciality->name);

        foreach($speciality->profiles as $profile)
        {
            $userIdList[] = $profile['id'];
            $this->info($profile->email);
        }

        print_r($userIdList);

        if($method == 'clear')
        {
            $kgeResultList = QuizeResultKge::whereIn('user_id', $userIdList)->get();

            foreach ($kgeResultList as $result)
            {
                Log::info('clear:kge:result:bydiscipline', [
                    'speciality.id' => $specialityId,
                    'user_id'   => $result->user_id,
                    'payed'     => $result->payed,
                    'value'     => $result->value,
                    'points'    => $result->points,
                    'gpi'       => $result->gpi,
                    'letter'    => $result->letter
                ]);
                $result->delete();
            }

            $this->warn('Kge results removed');
        }
    }
}
