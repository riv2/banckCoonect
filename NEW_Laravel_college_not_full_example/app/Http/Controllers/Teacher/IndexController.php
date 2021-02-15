<?php

namespace App\Http\Controllers\Teacher;

use App\Role;
use App\Services\Domain;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Auth;
use Illuminate\Support\Facades\Log;

class IndexController extends Controller
{
    public function index(Request $request)
    {
        if( \Session::has('flash_message') )
        {
            $message = \Session::get('flash_message');
            \Session::flush();
            \Session::flash('flash_message', $message);
        }

        if(!Auth::check())
        {
            return redirect()->route('teacherLogin');
        }
        else
        {
            return view('pages.dashboard');
        }

        //return view('pages.index');
    }

    /**
     * @param Request $request
     * @return $this
     */
    public function postLogin(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->has('remember'), Role::NAME_TEACHER)) {

            if (Auth::user()->status == '0') {
                \Auth::logout();
                return redirect()->back()
                    ->withErrors(__("Your account is still activated, please check your mailbox") );
            }

            return redirect()->route('teacherDashboard');
        }

        return redirect()->back()->withErrors( __("A couple of email and password entered incorrectly. try again") );

    }

}
