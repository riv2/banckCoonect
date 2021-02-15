<?php

namespace App\Console\Commands\Fix;

use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixTimeShift extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:timeshift';

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
        $query = DB::connection('miras_restore')
            ->table('users')
            //->where('id', 11887)
            ->orderBy('id');

        $count = $query;
        $count = $count->count();

        $this->output->progressStart($count);

        $query->chunk(1000, function($users){
            foreach($users as $user)
            {
                User::where('id', $user->id)->update([
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                    'deleted_at' => $user->deleted_at
                ]);

                $this->output->progressAdvance();
            }

        });

        $this->output->progressFinish();
    }
}
