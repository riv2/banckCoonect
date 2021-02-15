<?php

namespace App\Console\Commands\Fix;

use App\Profiles;
use App\StudyGroup;
use Illuminate\Console\Command;

class FixGroups extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'groups:fix';

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
        $file = fopen(storage_path('import/fix_groups.csv'), 'r');
        $reportFile = fopen(storage_path('import/fix_groups_report.csv'), 'w');
        $rowCount = sizeof (file (storage_path('import/fix_groups.csv')));

        $this->output->progressStart($rowCount);

        while($row = fgetcsv($file, 0, ',', '"'))
        {
            $userId = $row[0];
            $group = $row[9];

            $userProfile = Profiles::where('user_id', $userId)->first();

            if($userProfile)
            {
                $studyGroup = StudyGroup::where('name', $group)->first();

                if(!$studyGroup)
                {
                    $studyGroup = new StudyGroup();
                    $studyGroup->name = $group;
                    $studyGroup->save();

                    $row[] = 'create group ' . $studyGroup->id;
                }

                Profiles::where('user_id', $userProfile->user_id)->update(['study_group_id' => $studyGroup->id]);
                $row[] = 'success';
            }
            else
            {
                $row[] = 'profile no found';
            }

            fputcsv($reportFile, $row);
            $this->output->progressAdvance();
        }

        fclose($reportFile);
        $this->output->progressFinish();

    }
}
