<?php

namespace App\Console\Commands;

use App\Chatter\Models\Ban;
use Illuminate\Console\Command;

class DropForumBan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'drop:forum_ban';

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
        Ban::
        whereRaw('HOUR(TIMEDIFF(now(), `created_at`)) >= `period`')->
        delete();
    }
}
