<?php

namespace App\Console\Commands;

use App\Discipline;
use App\Speciality;
use App\SpecialityDiscipline;
use Illuminate\Console\Command;

class RemoveSpeciality extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'specialities:remove {--year=}';

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
        $year = $this->option('year');
        $specialityList = Speciality::where('year', $year)->get();
        $specialityIdList = [];

        foreach($specialityList as $speciality)
        {
            $specialityIdList[] = $speciality->id;
        }

        SpecialityDiscipline::whereIn('speciality_id', $specialityIdList)->delete();
        Speciality::whereIn('id', $specialityIdList)->delete();

        $this->info('Removed specialities count: ' . count($specialityIdList));
    }
}
