<?php

namespace App\Console\Commands;

use App\Speciality;
use App\SpecialityDiscipline;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ClearSpeciality extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'speciality:clear';

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
        $specialityList = Speciality::where('year', '<', 2019)->get();

        $idList = [];
        foreach ($specialityList as $speciality)
        {
            $idList[] = $speciality->id;
        }

        SpecialityDiscipline::whereIn('speciality_id', $idList)->delete();
        Speciality::where('year', '<', 2019)->delete();
        Log::info('Clear speciality', $idList);
    }
}
