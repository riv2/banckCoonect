<?php

namespace App\Console\Commands;

use App\Discipline;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

class ImportDisciplines extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:disciplines';

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
        $disciplines = Excel::load(storage_path('import/Disciplines.xlsx'))->toArray();

        foreach ($disciplines as $discipline) {
            $d = Discipline::find($discipline['id']);
            $d->sector_id = (int)$discipline['sector_id'];
            $d->save();
        }
    }
}
