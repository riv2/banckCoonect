<?php

namespace App\Console\Commands\Fix;

use App\Profiles;
use App\StudyGroup;
use Illuminate\Console\Command;

class SetStudyGroupToStudent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'set:study_group:student';

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
        $groupList = StudyGroup::get();

        foreach ($groupList as $group)
        {
            $profiles = Profiles::where('team', $group->name);
            $profilesCount = $profiles;
            $profilesCount = $profilesCount->count();

            $profiles->update(['study_group_id' => $group->id]);

            $this->info('Group ' . $group->name . ' (' . $group->id . ') = ' . $profilesCount . ' users');
        }
    }
}
