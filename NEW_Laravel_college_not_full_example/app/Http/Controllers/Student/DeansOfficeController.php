<?php

namespace App\Http\Controllers\Student;

use App\{News,UserNews};
use App\Notification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Auth;
use App\User;
use Illuminate\Support\Facades\{Log,Response,Validator};
use Intervention\Image\Facades\Image;

class DeansOfficeController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $calendars['Праздничные дни'] = 'weekend';
        $user = Auth::user();
        if($user->mgApplication){
            $calendars['Магистратура'] = 'mg';
        } else {
            $form = $user->studentProfile->education_study_form;
            if($form == 'fulltime'){
                $calendars['Очная форма обучения'] = $form;
            } elseif($form == 'online'){
                $calendars['Дистанционная (онлайн) форма обучения'] = $form;
            } elseif($form == 'evening'){
                $calendars['Вечерняя форма обучения'] = $form;
            } elseif($form == 'extramural'){
                $calendars['Заочная форма обучения'] = $form;
            }
            
        }

        $profile = \App\Services\Auth::user()->studentProfile;

		return view('student.dean.index', compact('profile', 'calendars'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function notificationsList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page' => 'required|numeric',
            'count' => 'required|numeric'
        ]);

        if($validator->fails())
        {
            return Response::json($validator->errors());
        }

        $notificationList = Notification
            ::select('id', 'text')
            ->where('user_id', \App\Services\Auth::user()->id)
            ->orderBy('created_at', 'desc')
            ->offset(($request->input('page') - 1) * $request->input('count'))
            ->take($request->input('count'))
            ->get();

        return Response::json($notificationList);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function newsList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page' => 'required|numeric',
            'count' => 'required|numeric'
        ]);

        if($validator->fails())
        {
            return Response::json($validator->errors());
        }

        $newsList = News
            ::select(['id', 'title', 'text'])
            ->orderBy('created_at', 'desc')
            ->offset(($request->input('page') - 1) * $request->input('count'))
            ->take($request->input('count'))
            ->get();

        return Response::json($newsList);
    }

    public function downloadCalendar($type){
        $file = storage_path('app/academic_calendar/').$type.'.pdf';

        return response()->download($file);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCount( Request $request )
    {

        $iNotification = Notification::
        where('user_id',Auth::user()->id)->
        where('read',0)->
        count();

        $aUserNews = UserNews::
        select(['news_id'])->
        where('user_id',Auth::user()->id)->
        get()->
        toArray();

        $iNewsCount = News::
        select(['id'])->
        whereNotIn('id',$aUserNews)->
        count();

        return Response::json([
            'status'            => true,
            'countNotification' => $iNotification,
            'countNews'         => $iNewsCount
        ]);

    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function setNotificationCount( Request $request )
    {

        DB::table('notifications')
            ->where('user_id', Auth::user()->id)
            ->update(['read' => 1]);

        return Response::json([
            'status' => true
        ]);

    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function setNewsCount( Request $request )
    {

        $aUserNews = UserNews::
        select(['news_id'])->
        where('user_id',Auth::user()->id)->
        get()->
        toArray();

        $aNewsCount = News::
        select(['id'])->
        whereNotIn('id',$aUserNews)->
        get();

        if( count($aNewsCount) > 0 )
        {
            foreach($aNewsCount as $item)
            {
                $oUserNews = new UserNews();
                $oUserNews->fill([
                    'user_id' => Auth::user()->id,
                    'news_id' => $item->id
                ]);
                $oUserNews->save();
                unset($oUserNews);

            }
        }
        unset($aUserNews,$aNewsCount);

        return Response::json([
            'status' => true
        ]);

    }


}
	
	
  