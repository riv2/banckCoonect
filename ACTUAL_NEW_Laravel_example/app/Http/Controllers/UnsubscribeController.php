<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use stdClass;

class UnsubscribeController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, $email)
    {
        if (!$request->hasValidSignature()) {
            return abort(403, 'Мы не можем отписать Вас от рассылки');
        }

        $partner = User::where('email', '=', $email)->first();

        if (!$partner) {
            return abort(403, 'Мы не можем отписать Вас от рассылки');
        }

        if ($partner->email_unsubs === true) {
            return abort(403, 'Вы уже отписаны от рассылки!');
        }

        $partner->email_unsubs = true;
        $partner->save();

        $paths = collect(config('view.paths'));

        View::replaceNamespace('errors', $paths->map(function ($path) {
            return "{$path}/errors";
        })->push(__DIR__.'/views')->all());

        return view('errors.503', [
            'title' => 'Вы успешно отписаны от рассылки',
            'code' => 'OK',
            'exception' => new \Exception('Вы успешно отписаны от рассылки'),
        ]);
    }
}
