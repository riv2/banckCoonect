<?php

namespace App\Http\Controllers\Admin;

use App\{
    User,
    Role,
    Poll,
    Profiles,
    PollsUser,
    PollAnswer,
    PollQuestion,
    PollUserAnswer
};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use App\Validators\AdminQuizCreateValidator;

class QuizController extends Controller
{
    /**
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('admin.pages.quiz.index');
    }

    /**
     * @return \Illuminate\View\View
     */
    public function createForm()
    {
        $users = [];

        return view('admin.pages.quiz.edit', compact('users'));
    }

    /**
     * @param integer $poll_id
     * @return \Illuminate\View\View
     */
    public function editForm($poll_id)
    {
        $poll = Poll::with('questions.answers')->find($poll_id);

        if (empty($poll)) {
            abort(404);
        }

        $categories = [
            Profiles::CATEGORY_MATRICULANT          => 'Абитуриент',
            Profiles::CATEGORY_STANDART             => 'Стандарт',
            Profiles::CATEGORY_STANDART_RECOUNT     => 'Стандарт (перезачеты)',
            Profiles::CATEGORY_TRAJECTORY_CHANGE    => 'Смена траектории',
            Profiles::CATEGORY_RETAKE_ENT           => 'Пересдача ЕНТ',
            Profiles::CATEGORY_TRANSIT              => 'Транзит',
            Profiles::CATEGORY_TRANSFER             => 'Переводник',
        ];

        $courses = [
            Profiles::EDUCATION_COURSE_1 => 1,
            Profiles::EDUCATION_COURSE_2 => 2,
            Profiles::EDUCATION_COURSE_3 => 3,
            Profiles::EDUCATION_COURSE_4 => 4,
            Profiles::EDUCATION_COURSE_5 => 5,
            Profiles::EDUCATION_COURSE_6 => 6,
        ];

        $roles = [
            Role::NAME_CLIENT   => 'Студент',
            Role::NAME_GUEST    => 'Гость',
        ];

        $studyForms = [
            Profiles::EDUCATION_STUDY_FORM_FULLTIME     => 'Очная',
            Profiles::EDUCATION_STUDY_FORM_ONLINE       => 'Онлайн',
            Profiles::EDUCATION_STUDY_FORM_EVENING      => 'Вечерняя',
            Profiles::EDUCATION_STUDY_FORM_EXTRAMURAL   => 'Заочная',
        ];

        $studentGroups = Profiles::getAllTeam();

        $pollUsers = PollsUser::where('poll_id', $poll_id)->get();
        $users = [];

        foreach ($pollUsers as $user) {
            $users[$user->user_id] = true;
        }

        return view('admin.pages.quiz.edit', compact(
            'poll',
            'categories',
            'roles',
            'users',
            'courses',
            'studyForms',
            'studentGroups'
        ));
    }

    /**
     * @param Request $request
     * @param integer $poll_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function edit(Request $request, int $poll_id = null)
    {
        // validation data
        $obValidator = AdminQuizCreateValidator::make($request->all());

        if($obValidator->fails()) {
            return redirect()->back()->withInput()->withErrors($obValidator->messages());
        } else {
            if (empty($poll_id)) {
                $poll = new Poll();
            } else {
                $poll = Poll::find($poll_id);

                if (!$poll->is_active) {
                    $poll->questions()->delete();
                }
            }

            $poll->title_ru = $request->input('title_ru');
            $poll->title_kz = $request->input('title_kz');
            $poll->end_date = $request->input('end_date');
            $poll->is_required = $request->has('is_required');
            $poll->is_active = $request->has('is_active');
            $poll->save();

            if ($request->has('questions')) {
                $answers = [];

                foreach ($request->questions as $question) {
                    $pollQuestion = new PollQuestion();
                    $pollQuestion->poll_id = $poll->id;
                    $pollQuestion->text_ru = $question['text_ru'];
                    $pollQuestion->text_kz = $question['text_kz'];
                    $pollQuestion->is_multiple = isset($question['is_multiple'])? true : false;
                    $pollQuestion->is_custom_answer = isset($question['is_custom_answer'])? true : false;
                    $pollQuestion->save();

                    if (!empty($question['answers'])) foreach ($question['answers'] as $answer) {
                        $answers[] = [
                            'question_id' => $pollQuestion->id,
                            'text_ru' => $answer['text_ru'],
                            'text_kz' => $answer['text_kz'],
                        ];
                    }
                }

                PollAnswer::insert($answers);
            }

            return redirect()->route('admin.quiz.edit.show', ['quiz_id' => $poll->id]);
        }
    }

    /**
     * @param $poll_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clone($poll_id) {
        $poll = Poll::find($poll_id);

        if (empty($poll)) {
            abort(404);
        }

        $pollClone = new Poll();
        $pollClone->title_ru = $poll->title_ru . ' (clone)';
        $pollClone->title_kz = $poll->title_kz . ' (clone)';
        $pollClone->end_date = $poll->end_date;
        $pollClone->is_required = $poll->is_required;
        $pollClone->is_active = $poll->is_active;
        $pollClone->save();

        $answers = [];

        foreach ($poll->questions as $question) {
            $pollQuestion = new PollQuestion();
            $pollQuestion->poll_id = $pollClone->id;
            $pollQuestion->text_ru = $question->text_ru;
            $pollQuestion->text_kz = $question->text_kz;
            $pollQuestion->is_multiple = $question->is_multiple;
            $pollQuestion->is_custom_answer = $question->is_custom_answer;
            $pollQuestion->save();

            if (!$question->answers->isEmpty()) {
                foreach ($question->answers as $answer) {
                    $answers[] = [
                        'question_id' => $pollQuestion->id,
                        'text_ru' => $answer->text_ru,
                        'text_kz' => $answer->text_kz,
                    ];
                }
            }
        }

        PollAnswer::insert($answers);

        return redirect()->route('admin.quiz.edit.show', ['quiz_id' => $pollClone->id]);
    }

    /**
     * @return \Yajra\Datatables\Datatables
     * @throws \Exception
     */
    public function quizTable()
    {
        $polls = Poll::orderByActive();

        return Datatables::of($polls)
                ->addColumn('action', function($poll) {
                    return '<a href="' . route('admin.quiz.edit', ['quiz_id' => $poll->id]) . '" class="btn btn-default">
                                <i class="md md-edit"></i>
                            </a>
                            <a href="' . route('admin.quiz.remove', ['quiz_id' => $poll->id]) . '" class="btn btn-default">
                                <i class="md md-remove"></i>
                            </a>
                            <a href="' . route('admin.quiz.clone', ['quiz_id' => $poll->id]) . '" class="btn btn-default">
                                <i class="fa fa-copy"></i>
                            </a>
                            <a href="' . route('admin.quiz.report', ['quiz_id' => $poll->id]) . '" class="btn btn-default">
                                <i class="md md-file-download"></i>
                            </a>';
                })
                ->removeColumn('id')
                ->make(true);
    }

    /**
     * @param $poll_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function remove($poll_id){
        Poll::find($poll_id)->delete();

        return redirect()->back()->with('messages', [
            [
                'class' => 'alert-success',
                'message' => __('You have successfully removed the poll.'),
            ]
        ]);
    }

    /**
     * @param Request $request
     * @return json
     * @throws \Exception
     */
    public function quizUsersActiveTable(int $poll_id) {
        $users = PollsUser::select(['user_id'])->with('user')->where('poll_id', $poll_id)->get();

        return Datatables::of($users)
            ->addColumn('name', function ($user){
                return $user->user->name ?? '';
            })
            ->addColumn('action', function ($user){
                return '<button class="btn btn-default remove-active-user" type="button" data-user-id="' . $user->user_id . '">
                            <i class="md md-remove-circle"></i>
                        </button>';
            })
            ->removeColumn('user_id')
            ->removeColumn('user')
            ->make(true);
    }

    /**
     * @param Request $request
     * @param int $poll_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function userActiveRemove(Request $request, int $poll_id) {
        PollsUser::where('poll_id', $poll_id)->where('user_id', $request->input('user_id', 0))->delete();

        return response()->json(['status' => 'success']);
    }

    /**
     * @param int $poll_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function userActiveClear(int $poll_id) {
        PollsUser::where('poll_id', $poll_id)->delete();

        return response()->json(['status' => 'success']);
    }

    /**
     * @param Request $request
     * @return json
     * @throws \Exception
     */
    public function quizUsersTable(Request $request){
        $categories = [
            Profiles::CATEGORY_MATRICULANT          => 'Абитуриент',
            Profiles::CATEGORY_STANDART             => 'Стандарт',
            Profiles::CATEGORY_STANDART_RECOUNT     => 'Стандарт (перезачеты)',
            Profiles::CATEGORY_TRAJECTORY_CHANGE    => 'Смена траектории',
            Profiles::CATEGORY_RETAKE_ENT           => 'Пересдача ЕНТ',
            Profiles::CATEGORY_TRANSIT              => 'Транзит',
            Profiles::CATEGORY_TRANSFER             => 'Переводник',
        ];

        $studyForms = [
            Profiles::EDUCATION_STUDY_FORM_FULLTIME     => 'Очная',
            Profiles::EDUCATION_STUDY_FORM_ONLINE       => 'Онлайн',
            Profiles::EDUCATION_STUDY_FORM_EVENING      => 'Вечерняя',
            Profiles::EDUCATION_STUDY_FORM_EXTRAMURAL   => 'Заочная',
        ];

        $filters = [
            'name'          => $request->input('name', null),
            'roles'         => $request->input('role', null),
            'categories'    => $request->input('categories', []),
            'course'        => $request->input('course', []),
            'study_form'    => $request->input('study_form', []),
            'group'         => $request->input('group', []),
        ];

        if (empty($filters['roles'])) {
            $filters['roles'] = [
                Role::NAME_CLIENT,
                Role::NAME_GUEST,
            ];
        }

        $users = User::getUserListForPollAdmin($filters);

        return Datatables::of($users)
            ->addColumn('role', function ($user){
                $roles = [];

                foreach ($user->roles as $role) {
                    $roles[] = $role->title_ru;
                }

                return implode(', ', $roles);
            })
            ->addColumn('category', function ($user) use ($categories) {
                return $categories[$user->studentProfile->category] ?? '';
            })
            ->addColumn('course', function ($user) {
                return $user->studentProfile->course ?? '';
            })
            ->addColumn('study_form', function ($user) use ($studyForms) {
                return $studyForms[$user->studentProfile->education_study_form] ?? '';
            })
            ->addColumn('group', function ($user) {
                return $user->studentProfile->studyGroup->name ?? '';
            })
            ->addColumn('is_checked', function ($user) use ($request) {
                $isChecked = '';

                if (array_key_exists($user->id, $request->input('users', []))) {
                    $isChecked = 'checked';
                }

                return '<input type="checkbox" value="' . $user->id . '" class="quiz-user" ' . $isChecked . '>';
            })
            ->removeColumn('id')
            ->removeColumn('roles')
            ->removeColumn('student_profile')
            ->rawColumns(['is_checked'])
            ->make(true);
    }

    /**
     * @param Request $request
     * @param int $poll_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function editUsers(Request $request, int $poll_id) {
        $poll = Poll::find($poll_id);

        if (empty($poll)) {
            abort(404);
        }

        $users = $request->input('users', []);

        foreach ($users as $user_id => $value) {
            PollsUser::updateOrCreate(
                [
                    'user_id' => $user_id,
                    'poll_id' => $poll_id,
                ],
                [
                    'is_available' => true,
                    'is_completed' => false,
                ]
            );
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * @param Request $request
     * @param int $poll_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function insertAllUsers(Request $request, int $poll_id) {
        $poll = Poll::find($poll_id);

        if (empty($poll)) {
            abort(404);
        }

        $filters = [
            'name'          => $request->input('name', null),
            'roles'         => $request->input('role', null),
            'categories'    => $request->input('categories', []),
            'course'        => $request->input('course', null),
            'study_form'    => $request->input('study_form', null),
            'group'         => $request->input('group', null),
        ];

        if (empty($filters['roles'])) {
            $filters['roles'] = [
                Role::NAME_CLIENT,
                Role::NAME_GUEST,
            ];
        }

        $pollUsers = [];
        $users = User::getUserListForPollAdmin($filters)->select('id')->get();

        PollsUser::where('poll_id', $poll_id)->delete();

        foreach ($users as $user) {
            $pollUsers[] = [
                'user_id' => $user->id,
                'poll_id' => $poll_id,
                'is_available' => true,
                'is_completed' => false,
            ];
        }

        PollsUser::insert($pollUsers);

        return response()->json(['status' => 'success']);
    }

    /**
     * @param integer $poll_id
     * @return xls file
     */
    public function report($poll_id) {
        $poll = Poll::find($poll_id);

        if (empty($poll)) {
            abort(404);
        }

        ini_set("memory_limit", "256M");

        $categories = [
            Profiles::CATEGORY_MATRICULANT          => 'Абитуриент',
            Profiles::CATEGORY_STANDART             => 'Стандарт',
            Profiles::CATEGORY_STANDART_RECOUNT     => 'Стандарт (перезачеты)',
            Profiles::CATEGORY_TRAJECTORY_CHANGE    => 'Смена траектории',
            Profiles::CATEGORY_RETAKE_ENT           => 'Пересдача ЕНТ',
            Profiles::CATEGORY_TRANSIT              => 'Транзит',
            Profiles::CATEGORY_TRANSFER             => 'Переводник',
        ];

        $studyForms = [
            Profiles::EDUCATION_STUDY_FORM_FULLTIME     => 'Очная',
            Profiles::EDUCATION_STUDY_FORM_ONLINE       => 'Онлайн',
            Profiles::EDUCATION_STUDY_FORM_EVENING      => 'Вечерняя',
            Profiles::EDUCATION_STUDY_FORM_EXTRAMURAL   => 'Заочная',
        ];

        $excelData = [];
        $excelQuestions = [];
        $usersPoll = PollsUser::where('poll_id', $poll_id)->where('is_completed', true)->with(['user', 'poll.questions', 'poll.questions.userAnswers'])->get();
        $pollQuestions = PollQuestion::getReportByQuestion($poll_id);

        foreach ($usersPoll as $userPoll) {
            if (empty($userPoll->user)) {
                continue;
            }

            $userInfo = [
                'id'            => $userPoll->user->id,
                'name'          => $userPoll->user->name,
                'course'        => $userPoll->user->studentProfile->course,
                'category'      => $categories[$userPoll->user->studentProfile->category],
                'study_form'    => $studyForms[$userPoll->user->studentProfile->education_study_form],
                'group'         => $userPoll->user->studentProfile->studyGroup->name ?? '',
            ];

            foreach ($userPoll->poll->questions as $question) {
                $questionData = [
                    'ФИО' => $userInfo['name'],
                    'Категория' => $userInfo['category'],
                    'Курс' => $userInfo['course'],
                    'Форма обучения' => $userInfo['study_form'],
                    'Специальность' => $userInfo['group'],
                    'Вопрос' => $question->text_ru,
                ];

                foreach ($question->userAnswers as $key => $answer) {
                    if ($answer['user_id'] == $userInfo['id']) {
                        $questionData['Ответ ' . ($key+1)] = $answer->answer;
                    }
                }

                $excelData[] = $questionData;
            }
        }

        foreach ($pollQuestions as $pollQuestion) {
            $excelQuestions[] = [
                'Вопрос' => $pollQuestion->text_ru,
                'Ответ' => $pollQuestion->answer,
                'Количество' => $pollQuestion->count,
            ];
        }

        return Excel::create('Quiz_' . Carbon::now()->format('d_m_Y'), function($excel) use ($excelData, $excelQuestions) {
            $excel->sheet('Детальный отчет', function($sheet) use ($excelData)
            {
                $sheet->fromArray($excelData);
            });

            $excel->sheet('Статистика', function($sheet) use ($excelQuestions)
            {
                $sheet->fromArray($excelQuestions);
            });
        })->download('xls');
    }
}
