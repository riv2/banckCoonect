<?php

namespace App\Console\Commands;

use App\{
    StudentDiscipline,
    SyllabusTask,
    SyllabusTaskResult,
    SyllabusTaskResultAnswer
};
use App\Services\{SRORecalculationService};
use Illuminate\Console\Command;
use Illuminate\Support\Facades\{DB,Log};

class SRORecalculate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sro:recalculation';

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

        Log::info('Start command sro:recalculation ' . date('Y-m-d H:i:s'));

        SRORecalculationService::SRORecalculation();

        Log::info('End command sro:recalculation ' . date('Y-m-d H:i:s'));

    }
}
