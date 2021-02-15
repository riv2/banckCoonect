<?php

namespace App\Http\Middleware;

use App\Services\Auth;
use Closure;
use Illuminate\Support\Facades\Log;

class StepByStep
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        return $next($request);

        /*$profile = Auth::user()->studentProfile;
        $routeName = '';
        $params = [];

        if($profile && $profile->registration_step)
        {
            if($profile->registration_step == 'finish')
            {
                return $next($request);
            }
            else
            {
                $routeName = $profile->registration_step;

                if($routeName == 'bcApplicationPart')
                {
                    //$params['application'] = 'bachelor';
                    $params['part'] = Auth::user()->bc_application->part;
                }

                if($routeName == 'mgApplicationPart')
                {
                    //$params['application'] = 'master';
                    $params['part'] = Auth::user()->mg_application->part;
                }

                Log::info($request->url() . '   ' . route($routeName, $params));
                if($request->url() == route($routeName, $params))
                {
                    Log::info('ok');
                    return $next($request);
                }
            }
        }
        else
        {
            $routeName = 'userProfileID';

            if(($request->url() == route($routeName)) || !$profile->registration_step)
            {
                return $next($request);
            }
        }

        return redirect()->route($routeName, $params);*/
    }
}
