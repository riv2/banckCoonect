<?php

namespace App\Console\Commands;

use App\Profiles;
use App\StudentDiscipline;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SeparateSpecialization extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'specialization:separate';

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
        $studentLoginList = [
            'alisher_d',
            'saparaly_as',
            '77754444181',
            'pak_ok',
            'azimkhan_a',
            '77054352843',
            'kapralov_t',
            'tashmeto_n',
            '77059159595',
            'otebaeva_d',
            'pashenko_a',
            'rysbaev_m'
        ];

        Profiles
            ::whereHas('user', function($query) use($studentLoginList){
                $query->whereIn('email', $studentLoginList);
            })
            ->update(['education_speciality_id' => 98]);

        StudentDiscipline
            ::whereHas('user', function($query) use($studentLoginList){
                $query->whereIn('email', $studentLoginList);
            })
            ->where('discipline_id', 160)
            ->update(['discipline_id' => 1058]);

    }
}
