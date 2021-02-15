<?php

namespace App;

use App\Services\SearchCache;
use App\Teacher\ProfileTeacher;
use App\Services\Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    const AUTH_ONLINE_LOG = 'auth_online';

    const STUDENT_ONLINE_ACTIVITY = 'student_online_activity';

    const TEACHER_ONLINE_ACTIVITY = 'teacher_online_activity';

    const STUDENTS_ACTIVITIES_LOGS_TABLE = 'activity_logs:';

    public $guarded = [];

    protected $casts = [
        'properties' => 'collection',
    ];

    public function profileTeacher()
    {
        return $this->hasOne(ProfileTeacher::class, 'user_id', 'user_id');
    }

    public static function studentLogin()
    {
        $user = Auth::user();
        $currentTime = Carbon::now();

        if ($user->hasClientRole()){
            self::create([
                'log_type' => self::STUDENT_ONLINE_ACTIVITY,
                'user_id' => $user->id,
                'properties' => collect([
                    'from' => $currentTime->format('d-m-Y H:i:s'),
                    'to' => $currentTime->addMinutes(config('session.lifetime'))->format('d-m-Y H:i:s'),
                    'visited_pages' => []
                ]),
                'role' => 'student',
            ]);
        }
    }

    public static function teacherLogin()
    {
        $user = Auth::user();
        $currentTime = Carbon::now();

        if ($user->hasTeacherRole()){
            self::create([
                'log_type' => self::TEACHER_ONLINE_ACTIVITY,
                'user_id' => Auth::user()->id,
                'properties' => collect([
                    'from' => $currentTime->format('d-m-Y H:i:s'),
                    'to' => $currentTime->addMinutes(config('session.lifetime'))->format('d-m-Y H:i:s'),
                    'visited_pages' => []
                ]),
                'role' => 'teacher',
            ]);
        }
    }

    public static function logout()
    {
        if (!empty(Auth::user())){
            $activityUserLog = self::where('user_id', Auth::user()->id)
                ->whereIn('log_type', [self::STUDENT_ONLINE_ACTIVITY, self::TEACHER_ONLINE_ACTIVITY])
                ->orderByDesc('id')->first();

            if (isset($activityUserLog)){
                $properties = $activityUserLog->properties;
                $properties['to'] = Carbon::now()->format('d-m-Y H:i:s');

                $activityUserLog->properties = $properties;
                $activityUserLog->save();
            }
        }
    }

    public static function userVisitedPage($page)
    {
        if (!empty(Auth::user())){
            $activityLog = ActivityLog::where('user_id', Auth::user()->id)
                ->whereIn('log_type', [self::STUDENT_ONLINE_ACTIVITY, self::TEACHER_ONLINE_ACTIVITY])
                ->orderByDesc('id')->first();

            if (isset($activityLog)) {
                $properties = $activityLog->properties;
                $visited_pages = $properties['visited_pages'];
                $visited_pages[] = [
                    'page' => $page,
                    'time' => Carbon::now()->format('d-m-Y H:i:s'),
                    'url' => request()->url()
                ];
                $properties['visited_pages'] = $visited_pages;
                $activityLog->properties = $properties;
                $activityLog->save();

                $key = self::getKeyInCache(
                    $activityLog->created_at->year,
                    $activityLog->created_at->month,
                    Auth::user()->id,
                    $activityLog->created_at->day
                );
                SearchCache::refreshJSONString($key, collect($visited_pages)->toJson());
            }
        }
    }

    public static function getKeyInCache($year, $month, $user_id, $day)
    {
        return self::STUDENTS_ACTIVITIES_LOGS_TABLE . $year .':'. $month .':'.$user_id .':'. $day;
    }
}
