<?php

namespace App\Console\Commands;

use App\Discipline;
use App\Speciality;
use App\SpecialityDiscipline;
use Illuminate\Console\Command;

class getUnrelatedDisciplines extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'disciplines:cpecialities:unrelated:get {--year=2019}';

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

        $rows = [
            'speciality' => 14,
            'trajectory' => 15,
            'discipline_id' => 21,
            'course' => 13
        ];

        $year = $this->option('year');
        $file = fopen(storage_path('import/import_users_' . $year . '.csv'), 'r');
        $result = [];
        $this->output->progressStart(sizeof (file (storage_path('import/import_users_' . $year . '.csv'))));
        $nonSpec = [];
        $nonDisc = [];

        while($row = fgetcsv($file, 0, ',', "'"))
        {
            $startYear = $this->getStartYear($row[$rows['course']]);

            $hasDiscipline = false;
            $specialityId = $this->convertSpeciality($row[$rows['speciality']], $row[$rows['trajectory']], $startYear);

            $speciality = Speciality::with('disciplines')->where('id', $specialityId)->first();
            $discipline = Discipline::where('ex_id', $row[$rows['discipline_id']])->first();

            if($speciality && $discipline)
            {
                foreach($speciality->disciplines as $disciplineItem)
                {
                    if($disciplineItem->ex_id == $row[$rows['discipline_id']])
                    {
                        $hasDiscipline = true;
                    }
                }

                if(!$hasDiscipline)
                {
                    $result[$speciality->name][$discipline->name] = 'none';
                }
            }
            else
            {
                if(!in_array($row[$rows['speciality']], $nonSpec) && !$speciality)
                {
                    $nonSpec[] = $row[$rows['speciality']];
                    $this->error('Speciality not found: ' . $row[$rows['speciality']] . ' - ' . $startYear . '. ' . $row[$rows['trajectory']]);
                }

                if(!in_array($row[$rows['speciality']], $nonDisc) && !$discipline)
                {
                    $nonSpec[] = $row[$rows['speciality']];
                    $this->error('Discipline not found. ex_id: ' . $row[$rows['discipline_id']]);
                }
            }
            $this->output->progressAdvance();

        }
        $this->output->progressFinish();
        print_r( $result);
    }

    public function convertSpeciality($str, $trajectory, $year)
    {
        $codeChar = $this->convertCodeChar($str);
        if(!$codeChar || !in_array($codeChar, ['b', 'm']))
        {
            return null;
        }

        $mached = [];

        preg_match('|-.+|', $str, $mached);
        $specialityName = $mached[0] ?? null;
        $specialityName = str_replace('- ', '', $specialityName);

        if(!$specialityName)
        {
            return null;
        }

        $trajectory = $this->convertTrajectory($trajectory);

        if($trajectory) {
            $specialityName = $specialityName . '. ' . $trajectory;
        }

        if($specialityName == 'Финансы')
        {
            $specialityName = $specialityName . '. ' . 'Банковское дело';
        }

        if($specialityName == 'Дизайн' && $year == 2017)
        {
            $specialityName = $specialityName . '. ' . 'Архитектурный дизайн';
        }

        $speciality = Speciality
            ::where(\DB::raw('LOWER(name)'), $specialityName)
            ->where('code_char', $codeChar)
            ->where('year', $year)
            ->first();

        if(!$speciality)
        {
            return null;
        }

        return $speciality->id ?? null;
    }

    public function convertTrajectory($str)
    {
        $str = trim($str);

        if($str && !in_array($str,['А', 'А', 'А1', 'А1', 'A2', 'А2', 'А3', 'А3', 'А4', 'А4', 'А5', 'А5', 'А6', 'А6']))
        {
            return $str;
        }

        return '';
    }

    public function convertCodeChar($str)
    {
        $code = mb_substr($str, 1, 1);

        if($code == 'B' || $code == 'В') return 'b';
        if($code == 'M' || $code == 'М') return 'm';

        return false;
    }

    public function getStartYear($course)
    {
        $year = date('Y', time());
        return $year - $course + 1;
    }
}
