<?php

namespace App\Console\Commands;

use App\Discipline;
use App\Speciality;
use App\SpecialityDiscipline;
use App\Trajectory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportSpecialities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'specialities:import {--year=} {--study_form=} {--base_education=} {--trajectories=false}';

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
        $studyForm = $this->option('study_form');
        $baseEducation = $this->option('base_education');
        $withTrajectories = $this->option('trajectories') == 'true' ? true : false;

        if(!$year)
        {
            $this->error('Year parameter is empty');
            return;
        }

        if( !in_array($studyForm, ['fulltime', 'evening', 'parttime']) )
        {
            $this->error('Invalid study_form param. Want fulltime, evening or parttime');
            return;
        }

        if( !in_array($baseEducation, ['vo', 'spo', 's']) )
        {
            $this->error('Invalid study_form param. Want vo, spo or s');
            return;
        }

        $baseEducationId = 1;
        if($baseEducation == 'vo') $baseEducationId = 3;
        if($baseEducation == 'spo') $baseEducationId = 2;

        if($withTrajectories)
        {
            $trajectotySql = "spt.id is not null";

            if(!($baseEducationId == 3 && $year == 2018 && $studyForm == 'fulltime'))
            {
                if($year == 2016 && $studyForm == 'fulltime' && $baseEducationId == 1)
                {
                    $trajectotySql = $trajectotySql . " and dsz.name IN ('А')";
                }
                else
                {
                    $trajectotySql = $trajectotySql . " and dsz.name NOT IN ('А', 'А1', 'A2', 'А3')";
                }
            }
        }
        else
        {
            $trajectotySql = "spt.id is null";
        }

        $specialitySql = '';
        if($year == 2016 && $studyForm == 'fulltime' && $baseEducationId == 1)
        {
            $specialitySql = " and ds.name in ('Архитектурный дизайн', 'Дизайн костюма', 'Вычислительная техника и программное обеспечение')";
        }

        $rows = DB::connection('miras_full')->select("select
                ds.code as 'speciality_code',
                ds.name as 'speciality_name',
                ds.name_eng as 'speciality_name_en',
                ds.name_kaz as 'speciality_name_kz',
                ds.conferred_degree as 'conferred_degree',
                ds.conferred_degree_eng as 'conferred_degree_eng',
                ds.conferred_degree_kaz as 'conferred_degree_kaz',
                spi.lang_type as 'lang_type',+
                    case spi.category when 'COMMON' then 'ООД' when 'BASE' then 'БД' when 'PROFILE' then 'ПД' when 'ADDITIONAL' then 'ДВО' end as 'cicle',
                case spi.type when 'REQUIRED' then 'ОК' when 'ELECTIVE' then 'КВ' when 'PHYSICAL_CULTURE' then 'ФК' when 'NONE' then 'нет' when 'FACULTATIVE' then 'ФД' when 'RESEARCH_WORK' then 'ИРМ' end as 'mt_tk',
                spi.coursework as 'coursework',
                sf.form as 'study_form',
                d.name as 'discipline',
                d.id as 'discipline_id',
                dsz.name as 'trajectory',
                spi.exam_type as 'exam_type'
            
            from d_student_groups dsg
                     join study_plan_v2 sp on sp.speciality_id = dsg.speciality and sp.education_id = dsg.education and sp.study_form_id = dsg.study_form and dsg.year = sp.year
                     left join d_specialities ds on dsg.speciality = ds.id
                     join study_form sf on sf.id = sp.study_form_id
                     join d_educations e on sp.education_id = e.id
                     join study_plan_item_v2 spi on spi.study_plan_id = sp.id
                     left join foreign_langs fl on fl.id = spi.foreign_lang_id
                     join study_plan_item_discipline_v2 spid on spid.plan_item_id = spi.id
                     left join discipline d on d.id = spid.discipline_id
                     left join study_plan_trajectory spt on spt.study_plan_id = sp.id
                     left join d_specializations dsz on spt.specialization_id = dsz.id
            where
              dsg.year in (" . $year . ")
              and dsg.deleted = 0 and "
            . $trajectotySql .
            ' and e.index = ' . $baseEducationId .
            " and sf.form = '" . strtoupper($studyForm) . "'"
            . $specialitySql);

        $specCount = 0;
        $updateRowsCount = 0;
        $failDisciplinesCount = 0;

        foreach ($rows as $row)
        {
            $specialityName = $row->speciality_name;

            if($withTrajectories && isset($row->trajectory) && $row->trajectory)
            {
                $specialityName = $specialityName . '. ' . $row->trajectory;
            }

            $speciality = Speciality
                ::where('name', $specialityName)
                ->where('year', $year)
                ->first();

            if(!$speciality)
            {
                $speciality = new Speciality();
                $speciality->year = $year;
                $speciality->code_char = $this->parseCodeChar($row->speciality_code);
                $speciality->code = $this->parseCode($row->speciality_code);
                $speciality->name = $specialityName;
                $speciality->name_en = $row->speciality_name_en;
                $speciality->name_kz = $row->speciality_name_kz;
                $speciality->url = str_replace(' ', '_', strtolower($row->speciality_name_en) . '_' . $speciality->code . $speciality->code_char);
                $speciality->save();

                $specCount++;
            }
            else
            {
                $updateRowsCount++;
            }

            if($withTrajectories && isset($row->trajectory) && $row->trajectory)
            {
                $trajectory = Trajectory::where('name', $row->trajectory)->first();

                if(!$trajectory)
                {
                    $trajectory = new Trajectory();
                    $trajectory->name = $row->trajectory;
                    $trajectory->save();
                }

                $speciality->trajectories()->syncWithoutDetaching([$trajectory->id]);
            }

            $discipline = Discipline::where('ex_id', $row->discipline_id)->first();

            if($discipline)
            {
                $specialityDiscipline = SpecialityDiscipline
                    ::where('discipline_id', $discipline->id)
                    ->where('speciality_id', $speciality->id)
                    ->first();

                if(!$specialityDiscipline)
                {
                    $specialityDiscipline = new SpecialityDiscipline();
                    $specialityDiscipline->speciality_id = $speciality->id;
                    $specialityDiscipline->discipline_id = $discipline->id;
                }

                $specialityDiscipline->language_type = strtolower($row->lang_type);
                $specialityDiscipline->has_coursework = $row->coursework ?? false;
                $specialityDiscipline->discipline_cicle = $row->cicle;
                $specialityDiscipline->mt_tk = $row->mt_tk;
                $specialityDiscipline->exam_type = $row->exam_type ? strtolower($row->exam_type) : null;

                $specialityDiscipline->save();
            }
            else
            {
                $this->warn($row->discipline . ' - ' . $row->speciality_name);
                $failDisciplinesCount++;
            }

        }

        $this->info('Insert speciality count: ' . $specCount);
        $this->info('Updated rows count: ' . $updateRowsCount);
        $this->warn('Fail disciplines count: ' . $failDisciplinesCount);
    }

    /**
     * @param $specialityCode
     * @return |null
     */
    public function parseCode($specialityCode)
    {
        if(!$specialityCode)
        {
            return null;
        }

        preg_match('/[0-9]+/is', $specialityCode, $matches);

        return $matches[0][0] ?? null;
    }

    /**
     * @param $specialityCode
     * @return string|null
     */
    public function parseCodeChar($specialityCode)
    {
        if(!$specialityCode)
        {
            return null;
        }

        preg_match('/[A-ZА-Я]+/', $specialityCode, $matches);
        $char = $matches[0] ?? null;

        $result = null;

        if($char == 'М' || $char == 'M')
        {
            $result = Speciality::CODE_CHAR_MASTER;
        }

        if($char == 'В' || $char == 'B')
        {
            $result = Speciality::CODE_CHAR_BACHELOR;
        }

        return $result;
    }
}
