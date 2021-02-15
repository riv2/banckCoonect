<?php

namespace App\Console\Commands;

use App\Discipline;
use Illuminate\Console\Command;

class ExDisciplinesSpaces extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spaces:ex_disciplines';

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
        $fileImport = fopen(storage_path('import/ex_disciplines.csv'), 'r');
        $fileExport = fopen(storage_path('export/ex_disciplines.csv'), 'w');

        $i = 0;
        while( ($exRow = fgetcsv($fileImport)) !== false )
        {
            if($i === 0)
            {
                $exRow[] = '';

            }
            else
            {
                $inDb = (bool)Discipline::where('ex_id', $exRow[0])->count();
                $exRow[] = $inDb ? '+' : '-';
            }

            fputcsv($fileExport, $exRow);

            $i++;
        }
    }
}
