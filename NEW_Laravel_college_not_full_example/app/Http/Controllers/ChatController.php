<?php

namespace App\Http\Controllers;

use App\AdminUserDiscipline;
use App\Discipline;
use App\Profiles;
use App\Services\Auth;
use App\StudentWebcamChat;
use App\StudyGroupTeacher;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Response;
use function GuzzleHttp\Promise\all;

class ChatController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function studentChatView()
    {
        return view('chat.chat');
    }

    /**
     * @return array
     */
    public function getCurrentUser()
    {

        $user = User
            ::select(['p.fio as name', 'p.faceimg as photo', 'ur.role_id as role'])
            ->leftJoin('profiles as p', 'p.user_id', '=', 'users.id')
            ->leftJoin('user_role as ur', 'ur.user_id', '=', 'users.id')
            ->where('users.id', Auth::user()->id)
            ->groupBy(['p.fio', 'p.faceimg', 'ur.role_id'])
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

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function contactsInfo(Request $request)
    {
        $contacts = $request->input('contacts');

        $teachers = AdminUserDiscipline
            ::select(['admin_user_discipline.user_id as id', 'pt.fio as fio' ,'d.name as discipline', 'pt.photo'])
            ->leftJoin('students_disciplines as sd', 'sd.discipline_id', '=', 'admin_user_discipline.discipline_id')
            ->leftJoin('profile_teachers as pt', 'pt.user_id', '=', 'admin_user_discipline.user_id')
            ->leftJoin('disciplines as d', 'd.id', '=', 'sd.discipline_id')
            ->where('sd.student_id', Auth::user()->id)
            //->whereIn('study_group_teacher.user_id', $contacts[0])
            ->groupBy(['admin_user_discipline.user_id', 'd.name', 'pt.fio', 'pt.photo'])
            ->get();

        $result = [];

        foreach ($teachers as $teacher)
        {
            $result[$teacher->discipline][] = [
                'id' => $teacher->id,
                'fio' => $teacher->fio,
                'photo' => $teacher->photo ? url('/avatars/') . '/' . $teacher->photo : '',
                'newMessages' => 0,
                'missedCalls' => 0,
                'isOnline' => false
            ];
        }

        return response()->json($result);

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function setPeerId(Request $request)
    {
        Redis::set('peer:' . Auth::user()->id, $request->input('peer_id'));

        Redis::set('peer:' . $request->input('peer_id'), Auth::user()->id);

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
        $newWebcam->student_id = Auth::user()->id;
        $newWebcam->teacher_id = $request->get('teacher_id');
        $newWebcam->type = 'student';
        $newWebcam->save();

        $request->file('video')->move(public_path('webcamfiles'), $file_name);
    }


}
