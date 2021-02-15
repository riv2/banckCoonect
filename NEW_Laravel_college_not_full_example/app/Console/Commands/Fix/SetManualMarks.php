<?php

namespace App\Console\Commands\Fix;

use App\ManualResult;
use Illuminate\Console\Command;

class SetManualMarks extends Command
{
    protected $signature = 'fix:set_manual_marks';

    protected $description = 'Command description';

    public function handle()
    {
        ManualResult::chunk(100, function($manualResults) {
            foreach ($manualResults as $manualResult) {
                /** @var ManualResult $manualResult */

                if (empty($manualResult->studentDiscipline)) {
                    continue;
                }

                if ($manualResult->sro_new !== null) {
                    $manualResult->studentDiscipline->task_manual = true;
                    $manualResult->studentDiscipline->save();
                }

                if ($manualResult->exam_new !== null) {
                    $manualResult->studentDiscipline->test_manual = true;
                    $manualResult->studentDiscipline->save();
                }
            }
        });
    }
}
