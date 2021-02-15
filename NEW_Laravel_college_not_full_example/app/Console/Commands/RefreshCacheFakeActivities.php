<?php

namespace App\Console\Commands;

use App\ActivityLog;
use App\Services\SearchCache;
use Illuminate\Console\Command;

class RefreshCacheFakeActivities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'refresh_activities';

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
        ActivityLog::where('log_type',  ActivityLog::STUDENT_ONLINE_ACTIVITY)
                ->chunk(800, function ($rows) {
                    foreach ($rows as $log){
                        $key = ActivityLog::getKeyInCache(
                                $log->created_at->year,
                                $log->created_at->month,
                                $log->user_id,
                                $log->created_at->day
                            );
                        SearchCache::refreshJSONString($key, collect($log->properties['visited_pages'])->toJson());
                    }
            });
    }
}
