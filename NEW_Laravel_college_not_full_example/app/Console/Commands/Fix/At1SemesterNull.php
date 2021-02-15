<?php

namespace App\Console\Commands\Fix;

use App\PayDocument;
use App\StudentDiscipline;
use Illuminate\Console\Command;

class At1SemesterNull extends Command
{
    protected $signature = 'fix:at1semesterNull';

    protected $description = 'Command description';

    public function handle()
    {
        $ids = PayDocument::select(['student_discipline_id'])
            ->join('students_disciplines', 'pay_documents.student_discipline_id', '=', 'students_disciplines.id')
            ->whereRaw('DATE(pay_documents.created_at) = \'2019-01-13\'')
            ->where('type', PayDocument::TYPE_DISCIPLINE)
            ->where('status', PayDocument::STATUS_SUCCESS)
            ->whereNull('students_disciplines.at_semester')
            ->pluck('student_discipline_id')
        ->toArray();

        foreach ($ids as $id) {
            $this->info('SD ID=' . $id . ' semester ...');

            $SD = StudentDiscipline::where('id', $id)->first();

            $SD->at_semester = $SD->user->studentProfile->currentSemester();

            $this->info($SD->at_semester . ' ...');

            if (empty($SD->at_semester)) {
                die('ERROR. empty semester');
            }

            if (!$SD->save()) {
                die('ERROR. Not saved');
            }

            $this->info('saved.' . "\n");
        }
    }
}
