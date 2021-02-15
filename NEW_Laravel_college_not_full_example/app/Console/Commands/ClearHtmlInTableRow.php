<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\QuizAnswer;

class ClearHtmlInTableRow extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:html:table-row {{--table="quize_answers"}} {{--row="answer"}} {{--count=1}}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'remove not neccessary tags from table';

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
        $tableToClear = $this->option('table');
        $rowToClear = $this->option('row');
        $recordsToClear = $this->option('count');

        $cicles = 20;
        if ($recordsToClear < $cicles) {
            $cicles = 1;
        } else {
            $recordsToClear = round($recordsToClear / $cicles);
        }

        for ($i=0; $i < $cicles; $i++) { 
            $this->info($i . ' cleaning next ' . $recordsToClear . ' records');
            $records = DB::table($tableToClear)
                    ->select($rowToClear, 'id')
                    ->where([ [$rowToClear, 'like', '%' . '<!--[if gte mso' . '%'] ])
                    ->limit($recordsToClear)
                    ->get();

            foreach ($records as $record) {
                $pureString = htmlClearfromMsTags( $record->$rowToClear );
                DB::table($tableToClear)
                    ->where('id', $record->id)
                    ->update([$rowToClear => $pureString]);
            }
        }
        



    }

}

