<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $user = Auth::user();
        if( $user->usertype == 'Teacher'){
            $isTeacher = true;
        }

        // use this instagram access token generator http://instagram.pixelunion.net/
        $access_token = "1478877781.1677ed0.718035660de64d58adcc381484993fee";
        $photo_count = 36;
             
        $json_link="https://api.instagram.com/v1/users/self/media/recent/?";
        $json_link.="access_token={$access_token}&count={$photo_count}";

        $json = file_get_contents($json_link);
        $obj = json_decode($json, true, 512, JSON_BIGINT_AS_STRING);
        $instaFeeds = [];

        foreach ($obj['data'] as $feed) {
         
            $feed['text'] = $feed['caption']['text'];
            $feed['link'] = $feed['link'];
            $feed['like_count'] = $feed['likes']['count'];
            $feed['comment_count'] = $feed['comments']['count'];
            $feed['pic_src'] = str_replace("http://", "https://", $feed['images']['standard_resolution']['url']);
            $feed['time'] = date("F j, Y", $feed['caption']['created_time']);
            $feed['time'] = date("F j, Y", strtotime($feed['time'] . " +1 days"));

            if(strpos($feed['text'], "#Shymkent") == false) continue;

            //remove hashtags
            $re = '/#\S+\s*/';
            $feed['text'] = preg_replace($re, '', $feed['text']);
            $feed['text'] = substr($feed['text'], 0,400);

            $instaFeeds[] = $feed; 

        }


        return view('home', compact('instaFeeds', 'isTeacher'));
    }
}
