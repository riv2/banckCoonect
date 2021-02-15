<?php

namespace App\Http\Controllers\Student;

use App\Webcam;
use App\Services\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WebcamController extends Controller
{
    public function index(Request $request)
    {
        if($request->get('discipline_id') !== null){
            $newWebcam = new Webcam();
            $file_name = time() . str_random() . '.webm';
            $newWebcam->file_name = $file_name;
            $newWebcam->user_id = Auth::user()->id;
            $newWebcam->discipline_id = $request->get('discipline_id');
            $newWebcam->type = $request->get('test_type');
            $newWebcam->save();

            $request->file('video')->move(public_path('webcamfiles'), $file_name);
        }
    }
}
