<?php

namespace App\Http\Controllers\Admin;

use App\Notification;
use App\NotificationTemplate;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\{Log,Mail,Response};

class NotificationController extends Controller
{

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function send(Request $request,$users=null,$text=null)
    {

        if( !empty($users) ) {
            $userList = $users;
        } else {
            $userList = $request->input('users',[]);
        }
        
        if( empty($text) ) {
            $text = $request->input('text','');
        }

        $userModelList = User::whereIn('id', $userList)->get();

        if( !empty($userModelList) )
        {
            foreach ($userModelList as $user)
            {
                $notification = new Notification();
                $notification->user_id = $user->id;
                $notification->text = $text;
                $notification->save();

                /*
                Mail::send('emails.notification', ['notification' => $notification],
                    function ($message) use ($user) {
                        $message->from(getcong('site_email'), getcong('site_name'));
                        $message->to($user->email)->subject(__('Уведомление от Miras.app'));
                    });
                */

            }
        } else {

            return Response::json([
                'status'  => false,
                'message' => __('An error occurred while sending the notification')
            ]);
        }

        return Response::json([
            'status' => true,
            'message' => __('Success')
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function templateList(Request $request)
    {
        $userId = $request->input('user_id');

        $user = null;
        if($userId)
        {
            $user = User::where('id', $userId)->first();
        }

        return Response::json(NotificationTemplate::getListForAdmin($user));
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        Notification::where('id', $request->input('id'))->delete();

        return Response::json();
    }
}
