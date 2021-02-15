<?php

namespace App\Console\Commands;

use App\Discipline;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DisciplineMatch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'discipline:match';

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
        $disciplineList = Discipline::get();
            DB::connection('miras_full')
            ->table('discipline')
            ->get();

        $result = [];

        $count = 0;

        foreach ($disciplineList as $discipline)
        {
            $exDiscipline = DB::connection('miras_full')
                ->table('discipline')
                ->whereRaw("LOWER(name) = LOWER('" . $discipline->name . "')")
                ->first();

            if($exDiscipline)
            {
                if(!$discipline->ex_id)
                {
                    $discipline->ex_id = $exDiscipline->id;
                    $discipline->save();
                }
                $count++;
            }

            /*$result[] = implode(';', [
                '"'.($exDiscipline->name ?? '').'"',
                '""',
                '"'.($exDiscipline->id ?? '').'"',
                '"' . $discipline->name . '"',
                '"' . $discipline->credits . '"',
                '"' . $discipline->id . '"',
            ]);*/
        }

        $this->info($count);

        //file_put_contents(storage_path('export/discipline_match.csv'), implode("\r\n", $result));
    }
}
