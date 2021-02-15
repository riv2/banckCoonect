<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\PasswordReset;
use App\User;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Mail;
use Illuminate\Support\Facades\DB;

class PasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Create a new password controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }
    
    public function getEmail()
    {
        return view('auth.passwords.email');
    }

    /**
     * @param Request $request
     * @return $this
     */
    public function postEmail(Request $request)
    {
        $validator = \Validator::make($request->all(), [
           ['email' => 'required|email|exists:users,email']
        ]);

        if($validator->fails())
        {
           return redirect()->back()->withErrors($validator->errors());
        }

        $token = str_random(64);

        PasswordReset::where('email', $request->input('email'))->delete();
        PasswordReset::insert([
            'email' => $request->input('email'),
            'token' => $token,
            'created_at'    => DB::raw('now()')
        ]);

        Mail::send('emails.password',['email' => $request->only('email'), 'token' => $token], function($message)  use ($token, $request) {
           $message->subject(__('Your Password Reset Link'));
           $message->from(getcong('site_email'),getcong('site_name'));
           $message->to($request->input('email'));
        });

        \Session::flash('flash_message', __('Instructions for changing the password has been sent to your email'));

        return redirect()->route('login');
    }

    /**
     * Display the password reset view for the given token.
     *
     * @param  string  $token
     * @return \Illuminate\Http\Response
     */
    public function getReset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|exists:password_resets,token'
        ]);

        if($validator->fails())
        {
            abort(404);
        }

        return view('auth.passwords.reset')->with('token', $request->input('token'));
    }
    
    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postReset(Request $request)
    {
        $this->validate($request, [
            'token' => 'required|exists:password_resets,token',
            'password' => 'required|confirmed|min:6',
        ]);

        $token = PasswordReset::where('token', $request->input('token'))->first();
        $user = User::where('email', $token->email)->first();

        if($user)
        {
            $user->password = bcrypt($request->input('password'));
            $user->save();
            PasswordReset::where('token', $request->input('token'))->delete();

            \Session::flash('flash_message', __('Password changed. Login with a new password'));

            return redirect()->route('login');
        }

        abort(500);
    }
}