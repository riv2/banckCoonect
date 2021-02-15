<?php

namespace App\Http\Controllers\Admin;

use App\BcApplications;
use App\Http\Controllers\Controller;
use App\Profiles;
use App\QuizResult;
use App\Speciality;
use App\StudentDiscipline;
use App\StudyPlanLog;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;

class StudyPlanController extends Controller
{
    public function index()
    {
        $years = Speciality::getUniqueYears();
        $baseEducations = BcApplications::getBaseEducationsArray();
        $studyForms = Profiles::getStudyFormsArray();
        $specialities = Speciality::getArrayForSelect();
        $langs = Profiles::getLangsArray();

        return view('admin.study_plan.index', compact('years', 'studyForms', 'baseEducations', 'specialities', 'langs'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getListAjax(Request $request)
    {
        $searchData = User::getListForAdminStudyPlan(
            $request->input('search')['value'],
            $request->input('columns')[2]['search']['value'],
            $request->input('columns')[3]['search']['value'],
            $request->input('columns')[4]['search']['value'],
            $request->input('columns')[5]['search']['value'],
            $request->input('columns')[6]['search']['value'],
            $request->input('start', 0),
            $request->input('length', 10),
            $request->input('order')[0]['column'] ?? 0,
            $request->input('order')[0]['dir'] ?? 'asc'
        );

        return Response::json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $searchData['recordsTotal'],
            'recordsFiltered' => $searchData['recordsFiltered'],
            'data' => $searchData['data']
        ]);
    }

    public function make(Request $request)
    {
        $userIds = $request->input('users');

        if (empty($userIds)) {
            return Response::json(['error' => 'Необходимо выбрать студентов']);
        }

        foreach ($userIds as $userId) {
            StudentDiscipline::makePlan($userId, '2019-20.2', Auth::user()->id);
        }

        return Response::json(['success' => true]);
    }

    public function view(int $userId)
    {
        $user = User::where('id', $userId)->first();

        if(!$user)
        {
            abort(404);
        }

        $SDs = StudentDiscipline::getForPlanEdit($userId);

        $semester = $user->studentProfile->currentSemester();

        $semesterCredits = StudentDiscipline::creditsByPlanSemesters($userId);

        return view('admin.study_plan.view', compact('user', 'SDs', 'semester', 'semesterCredits'));
    }

    public function add(Request $request)
    {
        $userId = $request->input('userId');
        $semester = $request->input('semester');
        $disciplineId = $request->input('disciplineId');

        if (empty($userId) || empty($semester) || empty($disciplineId)) {
            return Response::json(['error' => 'Не достаточно данных']);
        }

        $user = User::where('id', $userId)->first();

        if (empty($user)) {
            return Response::json(['error' => 'Студент не существует']);
        }

        $SD = StudentDiscipline::getOne($userId, $disciplineId);

        if (empty($SD)) {
            return Response::json(['error' => 'SD не существует']);
        }

        if ($SD->plan_semester) {
            return Response::json(['error' => 'Ошибка. Дисциплина уже привязана к семестру. Перезагрузите страницу.']);
        }

        [$year, $semesterNumber] = StudentDiscipline::explodeSemester($semester);

        $credits = StudentDiscipline::plannedDisciplinesCredits($userId, $semester);

        // 1st or 2nd semester in year
        if (in_array($semesterNumber, [1, 2])) {
            $creditsLimit = $user->semester_credits_limit;
        }
        // 3rd semester
        elseif ($semesterNumber == 3) {
            $creditsLimit = StudentDiscipline::MAX_CREDITS_AT_SEMESTER3;
        } else {
            return Response::json(['error' => 'Ошибка. Неверный семестр.']);
        }

        // Limit reached
        if ($credits + $SD->discipline->ects > $creditsLimit) {
            return Response::json(['error' => 'Ошибка. Будет привышен лимит в кредитов в месяц. Обновите страницу.']);
        }

        // Unresolved dependencies
//        if (!empty($SD->dependencies)) {
//            return Response::json(['error' => 'Ошибка. Дисциплина имеет незакрытые зависимости.']);
//        }

        $SD->setPlanSemester($semester, Auth::user()->id);

        StudyPlanLog::addToPlan($SD, $semester, Auth::user()->id);

        return Response::json(['success' => true]);
    }

    public function delete(Request $request)
    {
        $userId = $request->input('userId');
        $semester = $request->input('semester');
        $disciplineId = $request->input('disciplineId');

        if (empty($userId) || empty($semester) || empty($disciplineId)) {
            return Response::json(['error' => 'Недостаточно данных']);
        }

        $SD = StudentDiscipline::getOne($userId, $disciplineId);

        if (empty($SD)) {
            return Response::json(['error' => 'SD не существует']);
        }

        if ($SD->plan_semester != $semester) {
            return Response::json(['error' => 'Ошибка. Дисциплина не привязана к данному семестру. Перезагрузите страницу.']);
        }

        if ($SD->clearPlanSemester(Auth::user()->id)) {
            return Response::json(['success' => true]);
        }

        return Response::json(['error' => 'Ошибка удаления дисциплины из плана']);
    }

    public function confirm(Request $request)
    {
        $userId = $request->input('userId');
        $semester = $request->input('semester');

        if (empty($userId) || empty($semester)) {
            return Response::json(['error' => 'Недостаточно данных']);
        }

        $SDs = StudentDiscipline::getBySemester($userId, $semester);

        foreach ($SDs as $SD) {
            if (!$SD->plan_admin_confirm) {
                $SD->adminConfirmPlanSemester($semester, Auth::user()->id);
            }
        }

        return Response::json(['success' => true]);
    }

    public function change(Request $request)
    {
        $userId = $request->input('userId');
        $semester = $request->input('semester');
        $disciplineId = $request->input('disciplineId');

        if (empty($userId) || empty($semester) || empty($disciplineId)) {
            return Response::json(['error' => 'Не достаточно данных']);
        }

        $user = User::where('id', $userId)->first();

        if (empty($user)) {
            return Response::json(['error' => 'Студент не существует']);
        }

        $SD = StudentDiscipline::getOne($userId, $disciplineId);

        if (empty($SD)) {
            return Response::json(['error' => 'SD не существует']);
        }

        [$year, $semesterNumber] = StudentDiscipline::explodeSemester($semester);

        $credits = StudentDiscipline::plannedDisciplinesCredits($userId, $semester);

        // 1st or 2nd semester in year
        if (in_array($semesterNumber, [1, 2])) {
            $creditsLimit = $user->semester_credits_limit;
        }
        // 3rd semester
        elseif ($semesterNumber == 3) {
            $creditsLimit = StudentDiscipline::MAX_CREDITS_AT_SEMESTER3;
        } else {
            return Response::json(['error' => 'Ошибка. Неверный семестр.']);
        }

        // Limit reached
        if ($credits + $SD->discipline->ects > $creditsLimit) {
            return Response::json(['error' => 'Ошибка. Будет привышен лимит в кредитов в месяц. Обновите страницу.']);
        }

        // Unresolved dependencies
//        if (!empty($SD->dependencies)) {
//            return Response::json(['error' => 'Ошибка. Дисциплина имеет незакрытые зависимости.']);
//        }

        $oldPlanSemester = $SD->plan_semester;

        $SD->changePlanSemester($semester, Auth::user()->id);

        StudyPlanLog::changeSemester($SD, $oldPlanSemester, $semester, Auth::user()->id);

        return Response::json(['success' => true]);
    }
}