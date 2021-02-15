<?php

namespace App\Http\Controllers\Teacher;

use App\AdminUserDiscipline;
use App\Profiles;
use App\Services\Auth;
use App\StudentWebcamChat;
use App\StudyGroupTeacher;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Response;

class TeacherChatController extends Controller
{
    public function teacherChatView()
    {
        return view('teacher.chat');
    }

    /**
     * @return array
     */
    public function getCurrentUser()
    {

        $user = User
            ::select(['p.fio as name', 'p.photo as photo', 'ur.role_id as role'])
            ->leftJoin('profile_teachers as p', 'p.user_id', '=', 'users.id')
            ->leftJoin('user_role as ur', 'ur.user_id', '=', 'users.id')
            ->where('users.id', Auth::user()->id)
            ->groupBy(['p.fio', 'p.photo', 'ur.role_id'])
            ->first();

        $data = [
            'user_id' => Auth::user()->id,
            'name' => $user->name,
            'photo' => $user->photo,
            'email' => Auth::user()->email,
            'type' => $user->role,
            'env' => env('APP_SOCKET_ENV')
        ];
        return $data;
    }

    public function contactsInfo(Request $request)
    {

        $profiles = AdminUserDiscipline
            ::select(['sd.student_id as id', 'p.fio as fio'])
            ->leftJoin('students_disciplines as sd', 'sd.discipline_id', '=', 'admin_user_discipline.discipline_id')
            ->leftJoin('profiles as p', 'p.user_id', '=', 'sd.student_id')
            ->where('admin_user_discipline.user_id', Auth::user()->id)
            //->where('study_group_teacher.study_group_id', 'p.study_group_id')
            ->groupBy(['sd.student_id', 'p.fio'])
            ->get();

        $data = [];
        foreach ($profiles as $profile) {
            $data[] = [
                'id' => $profile->id,
                'fio' => $profile->fio,
                'newMessages' => 0,
                'missedCalls' => 0,
                'isOnline' => false
            ];
        }

        return response()->json([
            'contacts_info' => $data,
        ]);

    }

    public function setPeerId(Request $request)
    {
        Redis::set('peer:' . Auth::user()->id, $request->input('peer_id'));

        return Response::json(['status' => 'success']);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPeerId(Request $request)
    {
        $peerId = Redis::get('peer:' . $request->input('contact_id'));

        return Response::json(['status' => 'success', 'peer_id' => $peerId]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * get caller info by id for display in modal
     */
    public function getCallerById(Request $request)
    {
        $id = $request->input('user_id');
        if($id) {
            $userInfo = User::select(['name'])->find($id);
            $userInfo['id'] = $id;

            return Response::json(['status' => 'success', 'userInfo' => $userInfo]);
        }

        return Response::json(['status' => 'failed']);
    }

    /**
     * @param Request $request
     */
    public function webcamPost(Request $request)
    {
        $newWebcam = new StudentWebcamChat();
        $file_name = time() . str_random() . '.webm';
        $newWebcam->file_name = $file_name;
        $newWebcam->student_id = $request->get('student_id');
        $newWebcam->teacher_id = Auth::user()->id;
        $newWebcam->type = 'teacher';
        $newWebcam->save();

        $request->file('video')->move(public_path('webcamfiles'), $file_name);
    }
}
