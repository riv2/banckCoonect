<?php

namespace App\Http\Controllers\Admin;

use App\AdminUserDiscipline;
use App\Profiles;
use App\StudentDiscipline;
use App\StudentGroupsSemesters;
use App\StudyGroup;
use App\StudyGroupTeacher;
use App\Teacher\ProfileTeacher;
use App\TeachersDisciplines;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Avatar;
use App\UserEducationDocument;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Validation\Rule;

class TeachersController extends MainAdminController
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getList()
    {
        $teacherList = ProfileTeacher::getTeacherListForAdmin();

        return view('admin.pages.teachers.list', [
            'teacherList' => $teacherList
        ]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request, $id)
    {
        ini_set('max_execution_time', 0);
        ini_set("memory_limit", "500M");
        $userTeacher = ProfileTeacher::getTeacherForAdmin($id);

        if (!$userTeacher) {
            abort(404);
        }

        $studyGroupList = StudyGroup::get();

        return view('admin.pages.teachers.edit', [
            'userTeacher' => $userTeacher,
            'educationDocument' => $userTeacher->educationDocumentFirst(),
            'studyGroupList' => $studyGroupList
        ]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function editPost(Request $request, $id)
    {
        $userTeacher = ProfileTeacher::getTeacherForAdmin($id);
        if(!$userTeacher)
        {
            abort(404);
        }

        $profileTeacher = $userTeacher->teacherProfile ?? null;

        if($profileTeacher) {
            $ruleList = [
                'iin' => 'nullable|numeric|min:12',
                'fio' => 'required|min:2',
                'bdate' => 'nullable|date',
                'doctype' => ['nullable', Rule::in(['pass', 'id'])],
                'docnumber' => 'nullable|min:2',
                'issuing' => 'nullable|min:2',
                'issuedate' => 'nullable|date',
                'sex' => ['nullable', Rule::in(['male', 'female'])],
                'mobile' => 'nullable|min:7',
                'status' => ['nullable', Rule::in([
                    ProfileTeacher::STATUS_MODERATION,
                    ProfileTeacher::STATUS_ACTIVE,
                    ProfileTeacher::STATUS_BLOCK,
                ])]
            ];

            if (!$profileTeacher->photo) {
                $ruleList['photo'] = 'nullable|image';
            }

            if ($request->input('education_document.level') && $request->input('education_document.level') != 'none') {
                $ruleList['education_document.level'] = ['nullable',Rule::in([
                    UserEducationDocument::LEVEL_HIGHER,
                    UserEducationDocument::LEVEL_SECONDARY_SPECIAL,
                    UserEducationDocument::LEVEL_SECONDARY
                ])];
                /*$ruleList['education_document.doc_number'] = 'required';
                $ruleList['education_document.doc_series'] = 'required';
                $ruleList['education_document.institution_name'] = 'required';*/
                $ruleList['education_document.date'] = 'nullable|date';
            }

            $validator = \Validator::make($request->all(), $ruleList);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->messages());
            }

            $profileTeacher->fill($request->all());
            $profileTeacher->status = $request->input('status');
            if ($profileTeacher->save() && $request->has('photo')) {
                Avatar::make($request->file('photo'))->save($profileTeacher->photo);
            }


            $document = UserEducationDocument::where('user_id', $profileTeacher->user_id)->first();

            if ($document) {
                if ($request->has('education_document.supplement_file')) {
                    $request->file('education_document.supplement_file')->move(
                        public_path('images/uploads/diploma'),
                        $document->supplement_file_name
                    );
                }
                if ($request->has('education_document.nostrification_file')) {
                    $request->file('education_document.nostrification_file')->move(
                        public_path('images/uploads/diploma'),
                        $document->nostrification_file_name
                    );
                }
            }
        }
        else
        {
            $ruleList = [
                'fio' => 'required|min:2'
            ];

            $validator = \Validator::make($request->all(), $ruleList);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->messages());
            }

            $profileTeacher = new ProfileTeacher();
            $profileTeacher->user_id = $id;
            $profileTeacher->fio = $request->input('fio');
            $profileTeacher->save();
        }

        $disciplineIdList = $request->input('disciplines');
        $userTeacher->teacherDisciplines()->sync($disciplineIdList);
        $inputGroups = $request->input('study_group_ids');
        StudyGroupTeacher::where('user_id', $userTeacher->id)->delete();
        $forInsert = [];

        if($inputGroups)
        {


            foreach ($inputGroups as $disciplineId => $groupList) {

                $dateFromList = $groupList['date_from'];
                $dateToList = $groupList['date_to'];

                foreach ($groupList['id'] as $k => $groupId) {

                    $studyGroup = StudyGroupTeacher
                        ::where('user_id', $userTeacher->id)
                        ->where('study_group_id', $groupId)
                        ->where('discipline_id', $disciplineId)
                        ->first();

                    if($studyGroup)
                    {
                        $studyGroup->date_from = !empty($dateFromList[$k]) ? date('Y-m-d', strtotime($dateFromList[$k])) : null;
                        $studyGroup->date_to = !empty($dateToList[$k]) ? date('Y-m-d', strtotime($dateToList[$k])) : null;
                        $studyGroup->save();
                    }
                    else
                    {
                        if ($groupId > 0 and $groupId !== ''){
                            $forInsert[] = [
                                'user_id' => $userTeacher->id,
                                'study_group_id' => $groupId,
                                'discipline_id' => $disciplineId,
                                'date_from' => !empty($dateFromList[$k]) ? date('Y-m-d', strtotime($dateFromList[$k])) : null,
                                'date_to' => !empty($dateToList[$k]) ? date('Y-m-d', strtotime($dateToList[$k])) : null,
                                'created_at' => DB::raw('NOW()'),
                                'updated_at' => DB::raw('NOW()'),
                            ];
                        }
                    }

                    StudentDiscipline
                        ::where('discipline_id', $disciplineId)
                        ->chunk(1000, function($list) use ($userTeacher){
                            foreach ($list as $item)
                            {
                                Redis::sadd('contacts:' . $item->user_id, $userTeacher->id);
                                Redis::sadd('contacts:' . $userTeacher->id, $item->user_id);
                            }
                        });


                }
            }
        }

        if($forInsert)
        {
            StudyGroupTeacher::insert($forInsert);
        }
        $request->session()->flash('flash_message', 'Изменения сохранены.');

        return redirect()->route('adminTeacherEdit', ['id' => $userTeacher->id]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id) {
        $profileTeacher = ProfileTeacher::getTeacherForAdmin($id);
        if(!$profileTeacher)
        {
            abort(404);
        }

        $profileTeacher->delete();

        return redirect()->route('adminTeacherList');
    }

    /**
     * @param Request $request
     * @return array
     */
    public function groupListByDisciplines(Request $request)
    {
        $disciplineIdList = $request->input('discipline_id_list', []);
        $resultGroupList = [];

        if($disciplineIdList)
        {
            $groupList = Profiles
                ::select(['study_groups.id as id', 'study_groups.name as name'])
                ->leftJoin('study_groups', 'study_groups.id', '=', 'profiles.study_group_id')
                ->leftJoin('students_disciplines', 'students_disciplines.student_id', '=', 'profiles.user_id')
                ->whereIn('students_disciplines.discipline_id', $disciplineIdList)
                ->groupBy(['study_groups.id', 'study_groups.name'])
                ->get();

            $excList = [];

            foreach ($groupList as $group)
            {
                $excList[] = $group->id;
                $resultGroupList[] = $group;
            }

            $resultGroupList[] = ['id' => 0, 'name' => '----------------------'];

            $groupList2 = StudentGroupsSemesters
                ::select(['study_groups.id as id', 'study_groups.name as name'])
                ->leftJoin('study_groups', 'study_groups.id', '=', 'student_groups_semesters.study_group_id')
                ->leftJoin('students_disciplines', 'students_disciplines.student_id', '=', 'student_groups_semesters.user_id')
                ->whereIn('students_disciplines.discipline_id', $disciplineIdList)
                ->where('semester', '2019-20.1')
                ->whereNotIn('student_groups_semesters.study_group_id', $excList)
                ->get();

            foreach ($groupList2 as $group)
            {
                $resultGroupList[] = $group;
            }
        }

        return $resultGroupList;
    }
}
