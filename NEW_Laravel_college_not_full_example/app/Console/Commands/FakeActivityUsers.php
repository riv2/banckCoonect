<?php

namespace App\Console\Commands;

use App\ActivityLog;
use App\Profiles;
use App\Services\SearchCache;
use App\Services\WebSocketService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class FakeActivityUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fake_activity';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    const START_ACTIVITY = 10;

    const END_ACTIVITY = 20;

    const LIMITS = [
        '10:00' => [
            'from' => 20,
            'to' => 50,
        ],
        '10:10' => [
            'from' => 50,
            'to' => 75,
        ],
        '10:20' => [
            'from' => 75,
            'to' => 150,
        ],
        '10:30' => [
            'from' => 150,
            'to' => 300,
        ],
        '10:40' => [
            'from' => 300,
            'to' => 600,
        ],
        '10:50' => [
            'from' => 600,
            'to' => 1000,
        ],
        '11:00' => [
            'from' => 1000,
            'to' => 1400,
        ],
        '12:00' => [
            'from' => 1400,
            'to' => 1800,
        ],
        '13:00' => [
            'from' => 1800,
            'to' => 2200,
        ],
        '14:00' => [
            'from' => 1800,
            'to' => 2200,
        ],
        '15:00' => [
            'from' => 2000,
            'to' => 2500,
        ],
        '16:00' => [
            'from' => 2000,
            'to' => 2500,
        ],
        '17:00' => [
            'from' => 2000,
            'to' => 2500,
        ],
        '18:00' => [
            'from' => 1500,
            'to' => 2000,
        ],
        '19:00' => [
            'from' => 800,
            'to' => 1500,
        ],
        '20:00' => [
            'from' => 500,
            'to' => 800,
        ],
        '20:10' => [
            'from' => 250,
            'to' => 500,
        ],
        '20:20' => [
            'from' => 100,
            'to' => 250,
        ],
        '20:30' => [
            'from' => 75,
            'to' => 100,
        ],
        '20:40' => [
            'from' => 50,
            'to' => 75,
        ],
        '20:50' => [
            'from' => 20,
            'to' => 50,
        ],
    ];
    const DAYS_OFF = [
        6,
        7
    ];

    /**
     * @param string $routeParams
     * @return array
     */
    const ROUTES = [
        'Главная' =>'home',
        'Обучение' =>'study',
        'Силабус' => 'studentSyllabus',
        'Экзамен' => 'studentExam',
        'СРО' => 'sroGetList',
        'Форум' => 'chatter.home',
        'Чат' => 'openChat'
    ];
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
        $time = Carbon::now();
        $hours = $time->format('H');

        $limit = self::getLimits($time);
        if ($hours >= self::START_ACTIVITY and $hours <= self::END_ACTIVITY and $limit != 0){
            $students =  Profiles::inRandomOrder()
                ->where('education_status', Profiles::EDUCATION_STATUS_STUDENT)
                ->where('check_level', 'or_cabinet')
                ->limit($limit)
                ->get();

            $idsToRedis = [];
            $dataToRedis = [];

            foreach ($students as $student){
                $studentInfo = self::addActyvityLogForStudent($student);
                if (isset($studentInfo)){
                    $idsToRedis[] = $studentInfo['id'];
                    $dataToRedis[] = $studentInfo;
                }
            }

            $currentTime = Carbon::now();
            $redisOnlineUsersIds = Redis::smembers('list:online_users');


            foreach ($redisOnlineUsersIds as $id){

                $activity = ActivityLog::where('user_id', $id)
                    ->orderBy('id','desc')
                    ->first();

                if (rand(0, 1) and isset($activity)){
                    if ($activity['properties']['to'] > $currentTime->format('d-m-Y H:i:s') and $activity->is_fake){
                        if (!in_array($id, $idsToRedis)){
                            $studentProfile = Profiles::find($id);
                            if (isset($studentProfile)){
                                $idsToRedis[] = $studentProfile->user_id;

                                $properties = $activity->properties;
                                $visited_pages = $properties['visited_pages'];
                                $visited_pages[] = self::getRandomPageForStudent($studentProfile);
                                $properties['visited_pages'] = $visited_pages;
                                $activity->properties = $properties;
                                $activity->save();

                                $dataToRedis[] = [
                                    'id' => $studentProfile->user_id,
                                    'name' => $studentProfile->fio,
                                    'photo' => $studentProfile->faceimg ?? null,
                                    'email' => $studentProfile->user->email ?? null,
                                ];
                            }
                        }
                    }
                }
            }

            Redis::del('list:online_users');

            $dataToNodeJsSockets = [];

            foreach ($dataToRedis as $item){
                $dataToNodeJsSockets[$item['id']] = [
                    'name' => $item['name'],
                    'photo' => $item['photo'],
                    'email' => $item['email'],
                    'type' => 2
                ];
                Redis::sadd('list:online_users' , $item['id']);
            }
            $client = new WebSocketService();

            $data = [
                'action' => 'fake_auth',
                'users' => $dataToNodeJsSockets
            ];
            $client->send($data);
        }
    }

    public function addActyvityLogForStudent($student)
    {
        $currentTime = Carbon::now();
        $activity = ActivityLog::where('user_id', $student->user_id)
            ->where('role', 'student')
            ->whereDate('updated_at', Carbon::today())
            ->orderBy('id','desc')
            ->first();
        $studentInfo = null;

        if (!isset($activity)){
            $startPage = [
                'page' => 'Главная',
                'time' => $currentTime->format('d-m-Y H:i:s'),
                'url' => route('home')
            ];
            $pageVisit =  self::getRandomPageForStudent($student);
            $activity = ActivityLog::create([
                'log_type' => ActivityLog::STUDENT_ONLINE_ACTIVITY,
                'user_id' => $student->user_id,
                'properties' => collect([
                    'from' => $currentTime->format('d-m-Y H:i:s'),
                    'to' => $currentTime->addMinutes(rand(30, 50))->format('d-m-Y H:i:s'),
                    'visited_pages' => [$startPage,$pageVisit]
                ]),
                'role' => 'student',
                'is_fake' => 1
            ]);
            $studentInfo = [
                'id' => $student->user_id,
                'name' => $student->fio,
                'photo' => $student->faceimg ?? null,
                'email' => $student->user->email ?? null
            ];
            $key = ActivityLog::getKeyInCache(
                $activity->created_at->year,
                $activity->created_at->month,
                $student->user_id,
                $activity->created_at->day
            );
            SearchCache::refreshJSONString($key, collect([$startPage, $pageVisit])->toJson());

        } else {
            if($activity->is_fake){

                if ($activity['properties']['to'] < $currentTime->addMinutes(rand(120, 240))->format('d-m-Y H:i:s')) {
                    $properties = $activity->properties;
                    $visited_pages = $properties['visited_pages'];
                    $visited_pages[] = self::getRandomPageForStudent($student);
                    $properties['visited_pages'] = $visited_pages;
                    $activity->properties = $properties;
                    $activity->save();

                    $studentInfo = [
                        'id' => $student->user_id,
                        'name' => $student->fio,
                        'photo' => $student->faceimg ?? null,
                        'email' => $student->user->email ?? null
                    ];
                    $key = ActivityLog::getKeyInCache(
                        $activity->created_at->year,
                        $activity->created_at->month,
                        $student->user_id,
                        $activity->created_at->day
                    );
                    SearchCache::refreshJSONString($key, collect($visited_pages)->toJson());
                }
            }
        }
        return $studentInfo;
    }

    public static function getRandomPageForStudent($student)
    {
        $page = array_rand(self::ROUTES);
        $currentTime = Carbon::now();

        if ($page === 'Силабус' ){
            $disciplineId = $student->disciplines()->first()->id ?? null;

            $url = route(self::ROUTES[$page], compact('disciplineId'));
        } elseif ($page === 'Экзамен'){
            $disciplineId = $student->disciplines()->first()->id ?? '56';

            $url = route(self::ROUTES[$page], ['id' => $disciplineId]);
        } else {
            $url = route(self::ROUTES[$page]);
        }
        return [
            'page' => $page,
            'time' => $currentTime->addMinutes(rand(1, 5))->format('d-m-Y H:i:s'),
            'url' => $url
        ];;
    }

    public static function getLimits($time)
    {
        $minutes = $time->format('i');
        $hours = $time->format('H');

        $dayOfWeek = $time->dayOfWeekIso;

        $key = $hours.':';
        $minute = floor($minutes / 10) * 10;
        $limit = 0;
        $key .= $minute;
        if (empty(self::LIMITS[$key])){
            $key = $hours.':00';
            if (!empty(self::LIMITS[$key])){
                $limit = rand(self::LIMITS[$key]['from'], self::LIMITS[$key]['to']);
            }
        } else {
            $limit = rand(self::LIMITS[$key]['from'], self::LIMITS[$key]['to']);
        }
        if (in_array($dayOfWeek, self::DAYS_OFF)){
            $limit /= 2;
        }
        return $limit;
    }
}