<?php

namespace App\Console\Commands\Export;

use App\BcApplications;
use App\MgApplications;
use App\Profiles;
use App\SpecialityDiscipline;
use App\StudentDiscipline;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class SemesterPayReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pay_report:by_semester';

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
        $fileName = storage_path('export/export_pay_by_semester.csv');
        $file = fopen($fileName, 'w');
        App::setLocale('ru');

        $count = Profiles::whereHas('user', function($query){
            $query->whereNull('deleted_at');
        })->whereIn('education_status', ['student', 'send_down'])->count();

        fputcsv($file, [
            'ID',
            'ФИО',
            'Категория',
            'Форма обучения',
            'Курс',
            'Группа',
            'Спец-ть',
            'Степень',
            'Утверждение учебного плана ( УМУ)',
            'Утверждение учебного плана студентом',
            'Кредитов утверждено (админом)',
            'Оплачено утвержденных(админом) кредитов',
            'Статус',
            //'Рекомен. Семестр 1й сем. Сумма кредитов',
            'Рекомен. Семестр 2й сем. Сумма кредитов',
            //'Вид практики 1 сем',
            //'Кредитность практики 1 сем',
            'Вид практики 2 сем',
            'Кредитность практики 2 сем',
            //'Сколько купил кредитов в 1м семестре',
            'Сколько купил кредитов во 2м семестре',
            //'Сколько оплатил в 1м семестре  за учебу (без онлайн)',
            'Сколько оплатил во 2м семестре  за учебу (без онлайн)',
            //'Сколько кредитов онлайн купил в 1м семестре',
            'Сколько кредитов онлайн купил во 2м семестре',
            //'сумма за онлайн  в 1м семестре',
            'сумма за онлайн  во 2м семестре',
            //'сумма оплаты за отработку практики 1сем ',
            'сумма оплаты за отработку практики 2сем '
        ]);

        $this->output->progressStart($count);

        Profiles::whereHas('user', function($query){
            $query->whereNull('deleted_at');
        })
            ->with('speciality')
            ->with('studyGroup')
            ->with('user')
            ->whereIn('education_status', ['student', 'send_down'])
            ->chunk(2000, function($profiles) use(&$file){

            foreach($profiles as $profile)
            {
                $creditPrice1 = $profile->user->getCreditPrice('2019-20.1');
                $creditPrice2 = $profile->user->getCreditPrice('2019-20.2');
                $creditPriceRemoteAccess = $profile->user->remote_access_price;
                $startSemester = (($profile->course-1) * 2) + 1;

                $creditsBySemester1 = $this->getCreditsBySemester($startSemester, $profile->user_id);
                $creditsBySemester2 = $this->getCreditsBySemester($startSemester + 1, $profile->user_id);
                $typeOfPractic1 = $this->typeOfPractic($startSemester, $profile->user_id);
                $creditSumOfPractic1 = $this->creditSumOfPractic($startSemester, $profile->user_id);
                $typeOfPractic2 = $this->typeOfPractic($startSemester + 1, $profile->user_id);
                $creditSumOfPractic2 = $this->creditSumOfPractic($startSemester + 1, $profile->user_id);
                $payedCreditsBySemesterNonRa1 = $this->payedCreditsBySemester($startSemester, $profile->user_id);
                $payedCreditsBySemesterNonRa2 = $this->payedCreditsBySemester($startSemester + 1, $profile->user_id);
                $payedMoneyBySemesterNonRa1 = $payedCreditsBySemesterNonRa1 * $creditPrice1;
                $payedMoneyBySemesterNonRa2 = $payedCreditsBySemesterNonRa2 * $creditPrice2;

                $payedCreditsBySemesterRa1 = $this->payedCreditsBySemester($startSemester, $profile->user_id, true);
                $payedCreditsBySemesterRa2 = $this->payedCreditsBySemester($startSemester + 1, $profile->user_id, true);
                $payedMoneyBySemesterRa1 = $payedCreditsBySemesterRa1 * $creditPriceRemoteAccess;
                $payedMoneyBySemesterRa2 = $payedCreditsBySemesterRa2 * $creditPriceRemoteAccess;

                $sumPracticalPayBySemester1 = $this->sumPracticalPayBySemester($startSemester, $profile->user_id);
                $sumPracticalPayBySemester2 = $this->sumPracticalPayBySemester($startSemester + 1, $profile->user_id);

                $statementStudyPlanSem2 = $this->statementStudyPlan($profile->user_id, $startSemester + 1);
                $statementStudyPlanByStudentSem2 = $this->statementStudyPlanByStudent($profile->user_id, $startSemester + 1);
                $statementCreditSem2 = $this->statementCredits($profile->user_id, $startSemester + 1);
                $statementCreditPayedSem2 = $this->statementCreditsPayed($profile->user_id, $startSemester + 1);
                $degree = $this->getDegree($profile->user_id);

                fputcsv($file, [
                    $profile->user_id,
                    $profile->fio,
                    __($profile->category),
                    __($profile->education_study_form . '_origin'),
                    $profile->course,
                    $profile->studyGroup->name ?? '',
                    $profile->speciality->name,
                    $degree,
                    $statementStudyPlanSem2 ? 'да' : 'нет',
                    $statementStudyPlanByStudentSem2 ? 'да' : 'нет',
                    $statementCreditSem2,
                    $statementCreditPayedSem2,
                    $profile->education_status,
                    //$creditsBySemester1,
                    $creditsBySemester2,
                    //$typeOfPractic1,
                    //$creditSumOfPractic1,
                    $typeOfPractic2,
                    $creditSumOfPractic2,
                    //$payedCreditsBySemesterNonRa1,
                    $payedCreditsBySemesterNonRa2,
                    //$payedMoneyBySemesterNonRa1,
                    $payedMoneyBySemesterNonRa2,
                    //$payedCreditsBySemesterRa1,
                    $payedCreditsBySemesterRa2,
                    //$payedMoneyBySemesterRa1,
                    $payedMoneyBySemesterRa2,
                    //$sumPracticalPayBySemester1,
                    $sumPracticalPayBySemester2
                ]);

                $this->output->progressAdvance();
            }

        });

        fclose($file);
        $this->output->progressFinish();
    }

    /**
     * @param $semester
     * @param $userId
     * @return |null
     */
    public function getCreditsBySemester($semester, $userId)
    {
        $creditsSum = StudentDiscipline
            ::select(DB::raw('SUM(disciplines.ects) as sum_credits'))
            ->leftJoin('disciplines', 'disciplines.id', '=', 'students_disciplines.discipline_id')
            ->where('student_id', $userId)
            ->where('recommended_semester', $semester)
            ->first();

        return $creditsSum->sum_credits ?? null;
    }

    /**
     * @param $semester
     * @param $userId
     */
    public function typeOfPractic($semester, $userId)
    {
        $practicList = StudentDiscipline
            ::select('disciplines.*')
            ->leftJoin('disciplines', 'disciplines.id', '=', 'students_disciplines.discipline_id')
            ->where('student_id', $userId)
            ->where('recommended_semester', $semester)
            ->where('disciplines.is_practice', true)
            ->get();

        return count($practicList) > 0 ? implode(',', $practicList->pluck('name')->toArray()) : '';
    }

    /**
     * @param $semester
     * @param $userId
     * @return |null
     */
    public function creditSumOfPractic($semester, $userId)
    {
        $creditsSum = StudentDiscipline
            ::select(DB::raw('SUM(disciplines.ects) as sum_credits'))
            ->leftJoin('disciplines', 'disciplines.id', '=', 'students_disciplines.discipline_id')
            ->where('student_id', $userId)
            ->where('recommended_semester', $semester)
            ->where('disciplines.is_practice', true)
            ->first();

        return $creditsSum->sum_credits ?? null;
    }

    /**
     * @param $semester
     * @param $userId
     * @return |null
     */
    public function payedCreditsBySemester($semester, $userId, $remoteAccess = false)
    {
        if( $remoteAccess === true )
        {
            $creditsSum = StudentDiscipline
                ::select(DB::raw('SUM(disciplines.ects) as sum_credits'))
                ->leftJoin('disciplines', 'disciplines.id', '=', 'students_disciplines.discipline_id')
                ->where('student_id', $userId)
                ->where('recommended_semester', $semester)
                ->where('remote_access', $remoteAccess);
        }
        else
        {
            $creditsSum = StudentDiscipline
                ::select(DB::raw('SUM(payed_credits) as sum_credits'))
                ->where('student_id', $userId)
                ->where('recommended_semester', $semester);
        }

        $creditsSum = $creditsSum->first();

        return $creditsSum->sum_credits ?? null;
    }

    /**
     * @param $semester
     * @param $userId
     * @return |null
     */
    public function sumPracticalPayBySemester($semester, $userId)
    {
        $creditsSum = DB::table('students_disciplines')
            ->select([DB::raw('SUM(disciplines_practice_pay.payed_sum) as pay_sum')])
            ->leftJoin('disciplines_practice_pay', 'disciplines_practice_pay.discipline_id', '=', 'students_disciplines.discipline_id')
            ->where('student_id', $userId)
            ->where('recommended_semester', $semester)
            ->where('disciplines_practice_pay.user_id', $userId)
            ->first();

        print_r($creditsSum->pay_sum );

        return $creditsSum->pay_sum ?? '';
    }

    /**
     * @param $studentId
     * @param $recommendedSemester
     * @return bool
     */
    public function statementStudyPlan($studentId, $recommendedSemester)
    {
        return (bool)StudentDiscipline
            ::where('student_id', $studentId)
            //->where('recommended_semester', $recommendedSemester)
            ->where('plan_admin_confirm', 1)
            ->count();
    }

    /**
     * @param $studentId
     * @param $recommendedSemester
     * @return bool
     */
    public function statementStudyPlanByStudent($studentId, $recommendedSemester)
    {
        return (bool)StudentDiscipline
            ::where('student_id', $studentId)
            //->where('recommended_semester', $recommendedSemester)
            ->where('plan_student_confirm', 1)
            ->count();
    }

    /**
     * @param $studentId
     * @param $recommendedSemester
     */
    public function statementCredits($studentId, $recommendedSemester)
    {
        $sum = StudentDiscipline
            ::select(DB::raw('SUM(disciplines.ects) as statement_credits'))
            ->leftJoin('disciplines', 'disciplines.id', '=', 'students_disciplines.discipline_id')
            ->where('students_disciplines.student_id', $studentId)
            //->where('students_disciplines.recommended_semester', $recommendedSemester)
            ->where('students_disciplines.plan_admin_confirm', 1)
            ->first();

        return $sum->statement_credits ?? 0;
    }

    /**
     * @param $studentId
     * @param $recommendedSemester
     * @return int
     */
    public function statementCreditsPayed($studentId, $recommendedSemester)
    {
        $sum = StudentDiscipline
            ::select(DB::raw('SUM(students_disciplines.payed_credits) as statement_credits_payed'))
            ->where('student_id', $studentId)
            //->where('recommended_semester', $recommendedSemester)
            ->where('plan_admin_confirm', 1)
            ->first();

        return $sum->statement_credits_payed ?? 0;
    }

    /**
     * @param $studentId
     * @return string
     */
    public function getDegree( $studentId )
    {
        $bcApplication = (bool)BcApplications::where('user_id', $studentId)->count();
        $mgApplication = (bool)MgApplications::where('user_id', $studentId)->count();

        if($bcApplication && $mgApplication)
        {
            return 'бакалавр / магистр';
        }

        if($bcApplication)
        {
            return 'бакалавр';
        }

        if($mgApplication)
        {
            return 'магистр';
        }

        return '';
    }
}
