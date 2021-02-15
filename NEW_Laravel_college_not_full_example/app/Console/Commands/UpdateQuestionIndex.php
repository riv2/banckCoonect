<?php

namespace App\Console\Commands;

use App\Discipline;
use Illuminate\Console\Command;

class UpdateQuestionIndex extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'question:index:update';

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
        $disciplineList = Discipline::whereHas('syllabuses')->get();

        foreach ($disciplineList as $discipline)
        {
            $discipline->updateQuestionIndex();
            $this->info($discipline->id);
        }
    }
}
