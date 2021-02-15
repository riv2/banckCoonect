<?php

namespace App\Http;

use App\Http\Middleware\CheckBalance;
use App\Http\Middleware\NoForumBanned;
use App\Http\Middleware\RegistrationFinish;
use App\Http\Middleware\RegistrationPaid;
use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        \App\Http\Middleware\TrustProxies::class,
        \App\Http\Middleware\LocaleMiddleware::class,
        
        \App\Http\Middleware\EncryptCookies::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        \Illuminate\Session\Middleware\StartSession::class,
        
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\AllDocsUploaded::class,
            \App\Http\Middleware\CheckRequiredPolls::class,
        ],

        'api' => [
            /*'throttle:60,1',*/
            'bindings',
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'admin' => \App\Http\Middleware\AdminAuthenticate::class,
//        'IsTeacher' => \App\Http\Middleware\IsTeacher::class,
        'teacher' => \App\Http\Middleware\Teacher::class,
        'isTeacherProfileComplete' => \App\Http\Middleware\Teacher\IsProfileComplete::class,
        'hasRole'   => \App\Http\Middleware\hasRole::class,
        'hasRoleIn'   => \App\Http\Middleware\hasRoleIn::class,
        'hasAccess'   => \App\Http\Middleware\hasAccess::class,
        'hasRight'   => \App\Http\Middleware\hasRight::class,
        'hasRightIn'   => \App\Http\Middleware\hasRightIn::class,
        'noForumbanned' => \App\Http\Middleware\NoForumBanned::class,
        'hasRegistrationPaid' => \App\Http\Middleware\RegistrationPaid::class,
        'stepByStep' => \App\Http\Middleware\StepByStep::class,
        'registrationFinish' => \App\Http\Middleware\RegistrationFinish::class,
        'checkBalance' => \App\Http\Middleware\CheckBalance::class,
        'hasNotAcademDebt' => \App\Http\Middleware\HasNotAcademDebt::class,
        'confirmMobile' => \App\Http\Middleware\ConfirmMobile::class,
        'requiredPolls' => \App\Http\Middleware\CheckRequiredPolls::class,
        'nomenclatureDate' => \App\Http\Middleware\NomenclatureCheckFileDate::class,
        'visitedPage' => \App\Http\Middleware\VisitedPage::class,
        'isStudent' => \App\Http\Middleware\IsStudent::class,

    ];
}
