<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Mail;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\Debug\ExceptionHandler as SymfonyExceptionHandler;
use App\Mail\ExceptionOccured;
use App\Services\Auth;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        if ($this->shouldReport($exception)) {
            $this->sendEmail($exception); // sends an email
        }

        parent::report($exception);
    }

    /**
     * Sends an email to the developer about the exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function sendEmail(Exception $exception)
    {
        try {
            $e = FlattenException::create($exception);

            $handler = new SymfonyExceptionHandler();

            if(isset(Auth::user()->id)) {
                $html = "<p>Executed by: <strong>" . Auth::user()->id . "</strong></p>";
            } else {
                $html = "";
            }
            $html .= $handler->getHtml($e);
            
            $message = $e->getMessage();

            if(    strpos($message, 'was only partially uploaded') !== false 
                || strpos($message, 'does not comply with RFC 2822, 3.6.2') !== false ) {
                return true;
            }

            if( config('app.debug') == false ) {
                Mail::send('emails.exception', ['error' => $html], function ($m) {
                    $m->to(config('app.mailForErrorReport'))->subject('Error catched');
                });
            }

        } catch (Exception $ex) {
            dd($ex);
        }
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($request->is('api/*'))
        {
            if(!in_array( $exception->getCode(), [404, 400]))
            {
                return Response::json([
                    'status' => 'fail',
                    'message' => $exception->getMessage()
                ], 500);
            }
        }

        return parent::render($request, $exception);
    }
}
