<?php

namespace App\Console\Commands\Fix;

use App\Discipline;
use Illuminate\Console\Command;

class UpdateDisciplinesName extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'disicpline:name:update';

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
        $file = fopen(storage_path('import/update_disciplines_name.csv'), 'r');
        $rowCount = sizeof (file (storage_path('import/update_disciplines_name.csv')));
        $this->output->progressStart($rowCount);

        while($row = fgetcsv($file, 0, ',', '"'))
        {
            $disciplineId = $row[0];

            if(!in_array($disciplineId, [1206, 2428]))
            {
                $disciplineNameRu = $row[1];
                $disciplineNameKz = $row[2];
                $disciplineNameEn = $row[3];

                $discipline = Discipline::where('id', $disciplineId)->withTrashed()->first();

                if($discipline)
                {
                    $discipline->name = $disciplineNameRu;
                    $discipline->name_kz = $disciplineNameKz;
                    $discipline->name_en = $disciplineNameEn;
                    $discipline->save();
                }
                else
                {
                    $this->warn('Discipline not found ' . $disciplineId);
                }
            }

            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
    }
}
