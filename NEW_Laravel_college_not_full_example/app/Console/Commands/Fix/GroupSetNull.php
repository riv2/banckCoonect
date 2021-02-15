<?php

namespace App\Console\Commands\Fix;

use App\Profiles;
use App\StudentGroupsSemesters;
use App\StudyGroup;
use Illuminate\Console\Command;

class GroupSetNull extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'group:set:null {--part=1}';

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
        $part = $this->option('part');

        $file = fopen(storage_path('import/group_set_null_' . $part . '.csv'), 'r');
        $rowCount = sizeof (file (storage_path('import/group_set_null_' . $part . '.csv')));
        $fileReport = fopen(storage_path('import/group_set_null_' . $part . '_report.csv'), 'w');

        $this->output->progressStart($rowCount);

        while($row = fgetcsv($file, 0, ',', '"'))
        {
            $userId = $row[0];

            if($userId)
            {
                $area = '';
                $groupId = 0;
                $studentGroupSemestr = StudentGroupsSemesters
                    ::where('user_id', $userId)
                    ->where('semester', '2019-20.1')
                    ->first();

                if($studentGroupSemestr)
                {
                    $area = 'student_groups_semester';

                    $groupId = $studentGroupSemestr->study_group_id;

                    $studentGroupSemestr->study_group_id = null;
                    $studentGroupSemestr->save();
                }
                else
                {
                    $profile = Profiles
                        ::where('user_id', $userId)
                        ->first();

                    if($profile)
                    {
                        $area = 'profiles';
                        $groupId = $profile->study_group_id;

                        Profiles::where('id', $profile->id)->update(['study_group_id' => null]);
                    }
                    else
                    {
                        $row[] = 'profile not found';
                    }
                }

                if($area)
                {
                    $row[] = $area;
                    $row[] = $groupId;
                }
            }
            else
            {
                $row[] = 'user not found';
            }

            fputcsv($fileReport, $row);
            $this->output->progressAdvance();
        }

        fclose($fileReport);
        fclose($file);

        $this->output->progressFinish();
    }
}
