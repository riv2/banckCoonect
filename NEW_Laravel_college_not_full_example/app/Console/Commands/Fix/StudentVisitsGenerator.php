<?php

namespace App\Console\Commands\Fix;

use App\OrderUser;
use App\Profiles;
use App\StudentCheckin;
use App\StudentDiscipline;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class StudentVisitsGenerator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'visits:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate visits for students';

    protected $from;

    protected $to;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->from = date('2019-09-01');
        $this->to = date('2020-02-28');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Exception
     */
    public function handle()
    {
        $from = $this->from;
        $fileReport = fopen(storage_path('import/visit_generate_report.csv'), 'w');

        fputcsv($fileReport, [
            'user ID',
            'ФИО',
            'Форма обучения',
            'Курс',
            'Студент до',
            'Было визитов',
            'Максимум визитов',
            'Добавлено визитов',
            'Процент посещения'
        ]);

        $userCount = Profiles
            ::where('education_study_form', '=', 'fulltime')
            ->orWhere('education_study_form', '=', 'evening')->count();

        $this->output->progressStart($userCount);

        Profiles
            //::whereIn('user_id', [19331, 11366, 6465])
            ::where(function($q){
                $q->where('education_study_form', '=', Profiles::EDUCATION_STUDY_FORM_FULLTIME);
                $q->orWhere('education_study_form', '=', Profiles::EDUCATION_STUDY_FORM_EVENING);
            })
            ->chunk(1000, function($partProfiles) use($from, $fileReport){

                foreach ($partProfiles as $profile)
                {

                    $to = $this->getUserDateTo($profile->user_id);

                    $studentCheckins = StudentCheckin
                        ::where('student_id', $profile->user_id)
                        ->whereBetween('created_at', [$from, $to])
                        ->get();

                    $workingDays = $this->getDatesFromRange($this->from, $to);

                    $visitList = $this->getVisitsList($studentCheckins);
                    $notVisitsList = array_values(array_diff($workingDays, $visitList));
                    $maxHours = $this->getMaxStudyHours($profile->user_id);
                    $visitsCount = count($visitList);
                    $needVisitsHours = $this->getNeedVisitsHours($maxHours, $visitsCount);
                    $needVisitsHours = $needVisitsHours > 0 ? $needVisitsHours : 0;
                    $busyTimeList[$profile->user_id] = [];

                    $reportLine = [
                        $profile->user_id,
                        $profile->fio,
                        $profile->education_study_form,
                        $profile->course,
                        $to,
                        $visitsCount,
                        $maxHours,
                        $needVisitsHours,
                        $maxHours > 0 ? round(($needVisitsHours+$visitsCount) * 100 / $maxHours) : 0
                    ];

                    for ($l = 1; $l < $needVisitsHours; $l++) {

                        if(count($notVisitsList) > 0) {

                            $date = $notVisitsList[rand(0, count($notVisitsList) - 1)];
                            $time = false;
                            $tryCount = 0;

                            while (!$time && $tryCount < 10) {
                                $time = $this->getTimeInDay($date, $busyTimeList[$profile->user_id], $profile);

                                if (!$time) {
                                    $date = $notVisitsList[rand(0, count($notVisitsList) - 1)];
                                }

                                $tryCount++;
                            }

                            if ($time) {
                                StudentCheckin::insert([
                                    'is_generated' => 1,
                                    'created_at' => $date . ' ' . $time,
                                    'student_id' => $profile->user_id
                                ]);
                            }
                        }

                    }

                    fputcsv($fileReport, $reportLine);
                    $this->output->progressAdvance();
                }

            });

        fclose($fileReport);
        $this->output->progressFinish();
    }

    public function getUserDateTo($userId)
    {
        $orderUser = OrderUser
            ::select(['orders.id as id', 'orders.order_action_id', 'order_user.created_at as created_at'])
            ->leftJoin('orders', 'orders.id', '=', 'order_user.order_id')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->first();

        if($orderUser && in_array($orderUser->order_action_id, [1,2,3,9,12]))
        {
            return date('Y-m-d', strtotime($orderUser->created_at));
        }

        return $this->to;
    }

    public function getTimeInDay($date, &$busyTimeList, $profile)
    {
        $time = false;
        $hourList = [];
        $minutes = 0;

        if($profile->education_study_form == Profiles::EDUCATION_STUDY_FORM_FULLTIME)
        {
            if(in_array($profile->course, [1,4]) )
            {
                $hourList = ['02','03','04','05'];
                $minutes = 30;
            }
            else
            {
                $hourList = ['06','07','08','09','10','11'];
                $minutes = 40;
            }
        }

        if($profile->education_study_form == Profiles::EDUCATION_STUDY_FORM_EVENING)
        {
            $hourList = ['13','14','15'];
            $minutes = 10;
        }

        if($hourList)
        {
            foreach ($hourList as $hour)
            {
                if(isset($busyTimeList[$date]))
                {
                    if( !in_array($hour, $busyTimeList[$date]))
                    {
                        $busyTimeList[$date][] = $hour;
                        return $hour . ':' . rand($minutes - 10, $minutes + 10) . ':' . rand(0,59);
                    }
                }
                else
                {
                    $busyTimeList[$date][] = $hour;
                    return $hour . ':' . rand($minutes - 10, $minutes + 10) . ':' . rand(0,59);
                }

            }
        }

        return $time;
    }

    /**
     * @param $checkinList
     * @return array
     */
    public function getVisitsList($checkinList)
    {
        $visitList = [];

        foreach ($checkinList as $checkin) {
            $visitList[] = date(
                'Y-m-d',
                strtotime($checkin->created_at)
            );
        }

        return $visitList;
    }

    /**
     * @param $maxHours
     * @param $visitsCount
     * @return float|int
     */
    public function getNeedVisitsHours($maxHours, $visitsCount)
    {
        $needVisitsHours = 0;

        if($maxHours > 0) {
            $visitsPerc = round($visitsCount * 100 / $maxHours);
            $needVisitsPers = rand(75, 85) - $visitsPerc;

            $needVisitsHours = round($needVisitsPers * $maxHours / 100);
        }

        return $needVisitsHours;
    }

    /**
     * @return array
     */
    public function getProfiles()
    {
        $from = $this->from;
        $to = $this->to;

        $profiles = Profiles::where('education_study_form', '=', 'fulltime')
            ->orWhere('education_study_form', '=', 'evening')
            ->with(['studentCheckins' => function($q) use($from, $to) {
                $q->whereBetween('created_at', [$from, $to]);
            }])
            ->get();


        if($profiles) {
            $data = [];

            foreach ($profiles as $profile) {
                $visits_list = [];

                if($profile->studentCheckins) {
                    foreach ($profile->studentCheckins as $checkin) {
                        $visits_list[] = date(
                            'Y-m-d',
                            strtotime($checkin->created_at)
                        );
                    }
                }

                $maxHours = $this->getMaxStudyHours($profile->user_id);
                $visitsCount = count($visits_list);
                $needVisitsHours = 0;

                if($maxHours > 0) {
                    $visitsPerc = round($visitsCount * 100 / $maxHours);
                    $needVisitsPers = rand(75, 85) - $visitsPerc;

                    $needVisitsHours = round($needVisitsPers * $maxHours / 100);
                }

                $data[] = [
                    "user_id" => $profile->user_id,
                    "visits_list" => $visits_list,
                    "need_visits_hours" => $needVisitsHours
                ];
            }
            return $data;
        }
    }

    /**
     * @param $start
     * @param $end
     * @return array
     * @throws \Exception
     */
    public function getDatesFromRange($start, $end)
    {
        $holidays = [
            '2019-09-01',
            '2019-09-07',
            '2019-09-08',
            '2019-09-14',
            '2019-09-15',
            '2019-09-21',
            '2019-09-22',
            '2019-09-28',
            '2019-09-29',

            '2019-10-05',
            '2019-10-06',
            '2019-10-12',
            '2019-10-13',
            '2019-10-19',
            '2019-10-20',
            '2019-10-26',
            '2019-10-27',

            '2019-11-02',
            '2019-11-03',
            '2019-11-09',
            '2019-11-10',
            '2019-11-16',
            '2019-11-17',
            '2019-11-23',
            '2019-11-24',
            '2019-11-30',

            '2019-12-01',
            '2019-12-02',
            '2019-12-07',
            '2019-12-08',
            '2019-12-14',
            '2019-12-15',
            '2019-12-16',
            '2019-12-17',
            '2019-12-21',
            '2019-12-22',
            '2019-12-28',
            '2019-12-29',

            '2020-01-01',
            '2020-01-02',
            '2020-01-03',
            '2020-01-04',
            '2020-01-07',
            '2020-01-11',
            '2020-01-12',
            '2020-01-18',
            '2020-01-19',
            '2020-01-25',
            '2020-01-26',

            '2020-02-01',
            '2020-02-02',
            '2020-02-08',
            '2020-02-09',
            '2020-02-15',
            '2020-02-16',
            '2020-02-22',
            '2020-02-23',
        ];
        $array = [];
        $interval = new DateInterval('P1D');
        $realEnd = new DateTime($end);
        $realEnd->add($interval);
        $period = new DatePeriod(new DateTime($start), $interval, $realEnd);

        foreach ($period as $date) {

            if(!in_array($date->format('Y-m-d'), $holidays)) {
                $array[] = $date->format('Y-m-d');
            }
        }

        return $array;
    }

    public function getMaxStudyHours($userId)
    {
        $sum = StudentDiscipline
            ::select([DB::raw('SUM(disciplines.lecture_hours) as hours_sum')])
            ->leftJoin('profiles', 'profiles.user_id', '=', 'students_disciplines.student_id')
            ->leftJoin('disciplines', 'disciplines.id', '=', 'students_disciplines.discipline_id')
            ->whereRaw('(students_disciplines.recommended_semester - ((CAST(profiles.course AS DECIMAL(8, 2))-1) *2)) = 1')
            ->where('students_disciplines.student_id', $userId)
            ->first();

        return $sum->hours_sum ?? false;
    }

}
