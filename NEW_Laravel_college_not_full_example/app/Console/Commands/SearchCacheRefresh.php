<?php

namespace App\Console\Commands;

use App\ActivityLog;
use App\BcApplications;
use App\Discipline;
use App\DisciplinePayCancel;
use App\DiscountCategoryList;
use App\DiscountStudent;
use App\Module;
use App\Profiles;
use App\QuizResult;
use App\Role;
use App\Services\SearchCache;
use App\Speciality;
use App\SpecialityDiscipline;
use App\StudentDiscipline;
use App\StudyGroupTeacher;
use App\Trend;
use App\User;
use App\EmployeesUser;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class SearchCacheRefresh extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'search_cache:refresh {--type=all}';
    /*
        type may be:

        admin_users
        admin_poll_users
        admin_disciplines
        admin_matriculants
        admin_all_users
        admin_specialities
        admin_ent_winners
        admin_discount_users
        admin_discount_users_custom
        admin_quiz_results
     * */

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updating Redis search cache';

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
        app()->setLocale('ru');

        $type = $this->option('type');

        if($type== 'all' || $type == 'admin_users') $this->refreshAdminUsers();
        if($type== 'all' || $type == 'admin_guest_users') $this->refreshAdminGuestUsers();
        if($type== 'all' || $type == 'admin_poll_users') $this->refreshAdminPollUsers();
        if($type== 'all' || $type == 'admin_disciplines') $this->refreshAdminDisciplines();
        if($type== 'all' || $type == 'admin_matriculants') $this->refreshAdminMatriculants();
        if($type== 'all' || $type == 'admin_all_users') $this->refreshAdminAllUsers();
        if($type== 'all' || $type == 'admin_specialities') $this->refreshAdminSpecialities();
        if($type== 'all' || $type == 'admin_ent_winners') $this->refreshAdminEntWinners();
        if($type== 'all' || $type == 'admin_discount_users') $this->refreshAdminDiscountUsers();
        if($type== 'all' || $type == 'admin_discount_users_custom') $this->refreshAdminDiscountUsersCustom();
        if($type== 'all' || $type == 'admin_modules') $this->refreshAdminModules();
        if($type== 'all' || $type == 'admin_discipline_pay_cancel') $this->refreshDisciplinePayCancel();
        if($type== 'all' || $type == 'admin_quiz_results') $this->refreshQuizResults();
        if($type== 'all' || $type == 'admin_employees_users') $this->refreshEmployeesUsers();
        if($type== 'all' || $type == 'chat_contacts') $this->refreshChatContacts();
        /*if($type== 'all' || $type == 'admin_visits_results') $this->refreshProfilesVisits();*/
        if($type== 'all' || $type == 'admin_activity_teachers') $this->refreshTeacher();

    }

    /**
     * Only students
     * @return bool
     */
    public function refreshAdminUsers()
    {
        $cacheData = [];

        User::whereHas('studentProfile')
            ->with('studentProfile')
            ->orderBy('id')
            ->chunk(1000, function ($rows) use (&$cacheData) {
                foreach ($rows as $row) {
                    $status = '';

                    if($row->studentProfile) {
                        if ($row->studentProfile->status == Profiles::STATUS_ACTIVE) {
                            $status = 'Проверен';
                        }

                        if ($row->studentProfile->status == Profiles::STATUS_BLOCK) {
                            $status = 'Заблокирован';
                        }

                        if ($row->studentProfile->status == Profiles::STATUS_MODERATION) {
                            $status = 'Не проверен';
                        }

                        $cacheData[] = [
                            'id' => $row->id,
                            'email' => $row->email ?? '',
                            'name' => $row->name ?? '',
                            'mobile' => $row->studentProfile->mobile ?? '',
                            'status' => $status
                        ];
                    }
                }
            });

        return SearchCache::refreshByData('admin_users', $cacheData);
    }

    public function refreshAdminGuestUsers()
    {
        $cacheData = [];

        User::select(['users.id as id', 'users.created_at'])
            ->whereHas('studentProfile')
            ->with('studentProfile')
            ->leftJoin('user_role', 'user_role.user_id', '=', 'users.id')
            ->leftJoin('roles', 'user_role.role_id', '=', 'roles.id')
            ->where('roles.name', 'guest')
            ->orderBy('id')
            ->chunk(1000, function ($rows) use (&$cacheData) {
                foreach ($rows as $row) {
                    $status = '';

                    if ($row->studentProfile->status == Profiles::STATUS_ACTIVE) {
                        $status = 'Проверен';
                    }

                    if ($row->studentProfile->status == Profiles::STATUS_BLOCK) {
                        $status = 'Заблокирован';
                    }

                    if ($row->studentProfile->status == Profiles::STATUS_MODERATION) {
                        $status = 'Не проверен';
                    }

                    $cacheData[] = [
                        'id' => $row->id,
                        'phone' => $row->studentProfile->mobile ?? '',
                        'name' => $row->studentProfile->fio ?? '',
                        'created_at' => date('d.m.Y', strtotime($row->created_at))
                    ];
                }
            });

        return SearchCache::refreshByData('admin_guest_users', $cacheData);
    }

    public function refreshAdminPollUsers()
    {
        $filters['roles'] = [
            Role::NAME_CLIENT,
            Role::NAME_GUEST,
        ];

        $cacheData = [];

        User::searchUsersForQuizUsersTable($filters)
            ->chunk(1000, function ($users) use (&$cacheData) {
                foreach ($users as $user) {
                    $roles = [];

                    foreach ($user->roles as $role) {
                        $roles[] = $role->name;
                    }

                    $cacheData[] = [
                        'id' => $user->id,
                        'name' => $user->studentProfile->fio,
                        'roles' => implode(', ', $roles),
                        'category' => $user->studentProfile->category,
                        'course' => $user->studentProfile->course,
                        'study_form' => $user->studentProfile->education_study_form,
                        'group' => $user->studentProfile->studyGroup->name ?? '',
                    ];
                }
            });

        return SearchCache::refreshByData('admin_poll_users', $cacheData);
    }

    /**
     * All users
     * @return bool
     */
    public function refreshAdminAllUsers()
    {
        $cacheData = [];

        User::orderBy('id')
            ->chunk(1000, function ($rows) use (&$cacheData) {
                foreach ($rows as $row) {
                    $cacheData[] = [
                        'id' => $row->id,
                        'name' => $row->name ?? '',
                        'email' => $row->email ?? '',
                        'phone' => $row->phone ?? ''
                    ];
                }
            });

        return SearchCache::refreshByData(User::$adminRedisTable, $cacheData);
    }

    /**
     * @return bool
     */
    public function refreshAdminDisciplines()
    {
        $cacheData = [];

        Discipline
            ::orderBy('id')
            ->chunk(1000, function ($rows) use (&$cacheData) {
                foreach ($rows as $row) {
                    $cacheData[] = [
                        'id' => $row->id,
                        'name' => $row->name ?? '',
                        'ects' => $row->ects
                    ];
                }
            });

        return SearchCache::refreshByData(Discipline::$adminRedisTable, $cacheData);
    }

    /**
     * @return bool
     */
    public function refreshAdminMatriculants()
    {
        $cacheData = [];

        Redis::del('list:inspection');
        Redis::del('list:or_cabinet');
        User::withTrashed()
            ->with('studentProfile')
            ->whereHas('studentProfile', function ($query) {
                $query->whereHas('speciality');
                $query->where('registration_step', 'finish');
            })
            ->chunk(1000, function ($rows) use (&$cacheData) {
                foreach ($rows as $student) {
                    $application = null;
                    $degree = '';

                    if ($student->studentProfile->speciality->code_char == 'b') {
                        $application = $student->bcApplication ?? null;
                        $degree = 'Бакалавр';
                    }

                    if ($student->studentProfile->speciality->code_char == 'm') {
                        $application = $student->mgApplication ?? null;
                        $degree = 'Магистр';
                    }

                    if($application) {
                        $cacheData[] = [
                            'id' => $student->id,
                            'fio' => $student->studentProfile->fio,
                            'email' => $student->email,
                            'mobile' => $student->studentProfile->mobile,
                            'speciality' => $student->studentProfile->speciality->name,
                            'created_at' => date('Y', strtotime($student->created_at)),
                            'status' => $student->studentProfile->education_status,
                            'base_education' => __($application->education ? $application->education . '_origin' : 'нет'),
                            'education_form' => __($student->studentProfile->education_study_form . '_origin'),
                            'education_degree' => $degree,
                            'education_lang' => $student->studentProfile->education_lang,
                            'category' => $student->studentProfile->category,
                            'check_level' => $student->studentProfile->check_level,
                            'deleted' => $student->deleted_at === null ? 0 : 1
                        ];

                        Redis::sadd('list:' . $student->studentProfile->check_level, $student->id);
                    }
                }
            });

        return SearchCache::refreshByData(\App\User::$adminRedisMatriculantTable, $cacheData);
    }

    private function refreshAdminSpecialities()
    {
        $cacheData = [];

        Speciality::select(['id', 'code_char', 'code', 'name', 'year'])->orderBy('id')
            ->chunk(1000, function ($rows) use (&$cacheData) {
                foreach ($rows as $row) {
                    $cacheData[] = [
                        'id' => $row->id,
                        'email' => $row->full_code ?? '',
                        'name' => $row->name ?? '',
                        'phone' => $row->year ?? ''
                    ];
                }
            });

        return SearchCache::refreshByData(Speciality::$adminRedisTable, $cacheData);
    }

    private function refreshProfilesVisits()
    {
        $cacheData = [];

        Profiles::select(['profiles.user_id as id', 'profiles.fio as fio', 'study_groups.id as group'])
            ->leftJoin('users', 'users.id', '=', 'profiles.user_id')
            ->whereNull('users.deleted_at')
            ->leftJoin('study_groups', 'study_groups.id', '=', 'profiles.study_group_id')
            ->chunk(1000, function ($rows) use (&$cacheData) {
                foreach ($rows as $row) {
                    $cacheData[] = [
                        'id' => $row->id,
                        'fio' => $row->fio ?? '',
                        'group' => $row->group ?? '',
                    ];
                }
            });

        return SearchCache::refreshByData(Profiles::$profilesRedisTable, $cacheData);
    }

    private function refreshAdminEntWinners()
    {

        // TODO FIX HERE
        $years = [date('Y')]; //range(date('Y'), date('Y') + 1);

        // All trends
        $trends = Trend::getIdsAndNames();

        foreach ($years as $year) {
            $entList = [];
            foreach (array_keys($trends) as $trendId) {

                $specialityIds = Speciality::getIdsByTrendAndYear($trendId, $year);
                $winners = BcApplications::
                    select([
                        'bc_applications.user_id AS id',
                        'bc_applications.ent_total',
                        'profiles.fio',
                        'discount_student.status as status'
                    ])->
                    distinct()->
                    leftJoin('profiles', 'profiles.user_id', '=', 'bc_applications.user_id')->
                    whereIn('profiles.education_speciality_id', $specialityIds)->
                    leftJoin('discount_student', 'discount_student.user_id', '=', 'bc_applications.user_id')->
                    orderBy('bc_applications.ent_total', 'desc')->
                    whereNotNull('bc_applications.ent_total')->
                    leftJoin('users', 'users.id', '=', 'bc_applications.user_id')->
                    whereNull('users.deleted_at')->
                    limit(2)->
                    get()->
                    toArray();

                foreach ($winners as $winner) {
                    $winner['trendName'] = $trends[$trendId]['name'] . ' : ' . $trends[$trendId]['training_code'];
                    $winner['status'] = __($winner['status']);
                    $entList[] = $winner;
                }

            }

            SearchCache::refreshJSONString(BcApplications::$adminRedisTable . '_' . $year, $entList);
        }

        return true;
    }

    private function refreshAdminDiscountUsers()
    {
        $categories = DiscountCategoryList::select('id')->pluck('id');

        foreach ($categories as $categoryId) {
            $cacheData = [];

            DiscountStudent::select(['discount_student.id', 'discount_type_list.name_ru AS name', 'profiles.fio', 'discount_student.status', 'category_id', 'discount_type_list.id AS type_id'])
                ->leftJoin('discount_type_list', 'discount_type_list.id', '=', 'discount_student.type_id')
                ->leftJoin('profiles', 'profiles.user_id', '=', 'discount_student.user_id')
                ->where('category_id', $categoryId)
                ->orderBy('id')
                ->chunk(1000, function ($rows) use (&$cacheData) {
                    foreach ($rows as $row) {
                        $cacheData[] = [
                            'id' => $row->id,
                            'fio' => $row->fio ?? '',
                            'name' => $row->name ?? '',
                            'status' => __($row->status) ?? '',
                        ];
                    }
                });

            SearchCache::refreshByData(DiscountStudent::$adminRedisTable . $categoryId, $cacheData);
        }

        return true;
    }

    private function refreshAdminDiscountUsersCustom()
    {
        $cacheData = [];

        DiscountStudent::select(['discount_student.id', 'discount_type_list.name_ru AS name', 'profiles.fio', 'discount_student.status', 'category_id', 'discount_type_list.id AS type_id'])
            ->leftJoin('discount_type_list', 'discount_type_list.id', '=', 'discount_student.type_id')
            ->leftJoin('profiles', 'profiles.user_id', '=', 'discount_student.user_id')
            ->orderBy('id')
            ->chunk(1000, function ($rows) use (&$cacheData) {
                foreach ($rows as $row) {
                    $cacheData[] = [
                        'id' => $row->id,
                        'fio' => $row->fio ?? '',
                        'name' => $row->name ?? '',
                        'status' => __($row->status) ?? '',
                    ];
                }
            });

        SearchCache::refreshByData(DiscountStudent::$adminRedisCustomTable, $cacheData);

        return true;
    }

    private function refreshAdminModules()
    {
        $cacheData = [];

        Module::orderBy('id')
            ->chunk(1000, function ($rows) use (&$cacheData) {
                foreach ($rows as $row) {
                    $cacheData[] = [
                        'id' => $row->id,
                        'name' => $row->name ?? ''
                    ];
                }
            });

        return SearchCache::refreshByData(Module::$adminRedisTable, $cacheData);
    }

    private function refreshDisciplinePayCancel()
    {
        DisciplinePayCancel::orderBy('id')
            ->chunk(1000, function ($rows) {
                foreach ($rows as $row) {
                    $row->redisCacheRefresh();
                }
            });
    }

    private function refreshQuizResults()
    {
        $cacheData = [];

        QuizResult::select(['id', 'user_id'])
            ->with('user.studentProfile')
            ->chunk(1000, function ($rows) use (&$cacheData) {

                foreach ($rows as $row) {
                    /** @var QuizResult $row */
                    $cacheData[] = [
                        'id' => $row->id,
                        'fio' => $row->user->studentProfile->fio ?? ''
                    ];
                }
            });

        $this->info('Updated ' . count($cacheData) . ' rows');

        return SearchCache::refreshByData(QuizResult::$adminRedisTable, $cacheData);
    }

    private function refreshEmployeesUsers(){
        $cacheData = [];

        $users = EmployeesUser::all();

        foreach ($users as $key => $value) {
            if(!empty($value->user->positions)){
                foreach ($value->user->positions as $k => $val) {
                    $cacheData[] = [
                        'id' => $value->user_id,
                        'department' => $val->position->department->id
                    ];
                }
            }
        }

        return SearchCache::refreshByData('admin_employees_users', $cacheData);
    }

    private function refreshChatContacts() {

        $this->output->progressStart(Profiles::count());

        /*StudentDiscipline::
        select(['students_disciplines.student_id as student_id', 'admin_user_discipline.user_id as teacher_id'])
        ->leftJoin('admin_user_discipline', 'admin_user_discipline.discipline_id', '=', 'students_disciplines.discipline_id')
            ->where('students_disciplines.student_id', 8763)
        ->orderBy('students_disciplines.student_id')
        ->groupBy(['students_disciplines.student_id', 'admin_user_discipline.user_id'])
        ->chunk(1000, function($sdList) {
            foreach ($sdList as $sd) {
                if($sd->teacher_id != 18621)
                {
                    Redis::sadd('contacts:' . $sd->student_id, $sd->teacher_id);
                    Redis::sadd('contacts:' . $sd->teacher_id, $sd->student_id);
                }

                //$contactCount++;
                $this->output->progressAdvance();
            }

            //$this->info($contactCount);

        });*/

        Profiles::/*where('mobile', '+77026663393')->*/ chunk(1000, function ($profiles) {
            foreach($profiles as $profile)
            {
                $contactCount = 0;

                $teachers = StudyGroupTeacher
                    ::select(['study_group_teacher.user_id as id'])
                    ->leftJoin('students_disciplines as sd', 'sd.discipline_id', '=', 'study_group_teacher.discipline_id')
                    ->leftJoin('disciplines as d', 'd.id', '=', 'sd.discipline_id')
                    ->where('sd.student_id', $profile->user_id)
                    ->groupBy(['study_group_teacher.user_id'])
                    ->get();

                foreach ($teachers as $teacher)
                {
                    Redis::sadd('contacts:' . $profile->user_id, $teacher->id);
                    Redis::sadd('contacts:' . $teacher->id, $profile->user_id);

                    $contactCount++;
                }

                $this->info($contactCount);
                $this->output->progressAdvance();
            }
        });

        $this->output->progressFinish();


        /*$teachers = StudyGroupTeacher
        ::select(['study_group_teacher.user_id as id', 'pt.fio as fio' ,'d.name as discipline'])
        ->leftJoin('students_disciplines as sd', 'sd.discipline_id', '=', 'study_group_teacher.discipline_id')
        ->leftJoin('disciplines as d', 'd.id', '=', 'sd.discipline_id')
        ->where('sd.student_id', Auth::user()->id)
        ->whereIn('study_group_teacher.user_id', $contacts[0])
        ->groupBy(['study_group_teacher.user_id', 'd.name', 'pt.fio'])
        //->whereIn('sgt.user_id', 242)
        //->limit(10)
        ->get();*/
    }

    private function refreshTeacher()
    {
        $cacheData = [];

        $teachers = User::whereHas('roles', function ($q){
                return $q->where('name', 'teacher');
            })->get();
        foreach ($teachers as $teacher){

                $cacheData[] = [
                    'id' => $teacher->id,
                    'name' => $teacher->fio
                ];
        }
        return SearchCache::refreshByData(ActivityLog::TEACHER_ONLINE_ACTIVITY, $cacheData);
    }
}
