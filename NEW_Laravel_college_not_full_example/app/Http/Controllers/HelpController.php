<?php

namespace App\Http\Controllers;

use App\HelpRequest;
use Illuminate\Http\Request;
use App\Services\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class HelpController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function form()
    {
        if(isset(Auth::user()->helpRequests[0]->phone))
        {
            $phone = Auth::user()->helpRequests[0]->phone;
        }
        else {
            $phone = Auth::user()->getCallbackPhone(Auth::user()->getCurrentRole());
        }

        return view('help', ['phone' => $phone]);
    }

    /**
     * @param Request $request
     * @return $this
     */
    public function send(Request $request)
    {
        $phone = Auth::user()->getCallbackPhone(Auth::user()->getCurrentRole());

        if(!$phone)
        {
            $validator = Validator::make($request->all(), [
                'phone' => 'required'
            ]);

            if($validator->fails())
            {
                return redirect()->back()->withErrors($validator->messages());
            }

            $phone = $request->input('phone');
        }

        if(count(Auth::user()->helpRequests) === 0)
        {
            $helpRequest = new HelpRequest();
            $helpRequest->user_id = Auth::user()->id;
            $helpRequest->phone = $phone;
            $helpRequest->save();

            Mail::send( new \App\Mail\HelpRequest($helpRequest) );
        }

        return redirect()->route('help');
    }

    /**
     * @param $image, $fileExt, $tmpFilePath
     * @return string
     */
    /*
    public static function imageIDUpload($image, $fileExt, $tmpFilePath)
    {
        $fileName =  str_slug($image->getClientOriginalName(), '-').'-'.md5(str_random(7));
        $fileExtName = $fileName.$fileExt;
        $image->move(public_path($tmpFilePath), $fileName);
        if( \File::mimeType( public_path($tmpFilePath.$fileName) ) == 'application/octet-stream' ) {
            shell_exec('tifig  -v -p '.$image->getPathName().' ' .public_path($tmpFilePath).$fileExtName);
        } else {
            rename(public_path($tmpFilePath).$fileName, public_path($tmpFilePath).$fileExtName);
        }
        return $fileExtName;
    }
    */
}
