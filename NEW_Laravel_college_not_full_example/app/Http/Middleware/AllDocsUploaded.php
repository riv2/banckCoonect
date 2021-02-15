<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\User;
use \App\Role;
use App\BcApplications;
use App\MgApplications;
use Illuminate\Support\Facades\Route;
use App\ProfileDoc;

class AllDocsUploaded
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if(\App\Services\Auth::guest())
        {
            return $next($request);
        }

        if(\App\Services\Auth::user()->import_type == 'eng_test' ||
            \App\Services\Auth::user()->import_type == 'gos_test')
        {
            return $next($request);
        }

        if(
            isset(\App\Services\Auth::user()->studentProfile->registration_step) &&
            \App\Services\Auth::user()->studentProfile->registration_step == 'finish'
        ) {
            $routeURL = Route::getRoutes()->match($request)->getName();
            if ($routeURL == 'docsNeedToUpload' || $routeURL == 'userProfileID') return $next($request);

            if (Auth::check() && User::getCurrentRole() == Role::NAME_CLIENT) {
                $needUploadDocs = false;
                $mgApp = MgApplications::where('user_id', Auth::user()->id)->first();
                $bcApp = BcApplications::where('user_id', Auth::user()->id)->first();

                $docs = ProfileDoc::where('user_id', Auth::user()->id)->where('last', 1)->get();

                $docList = [];
                foreach ($docs as $doc) {
                    $docList[$doc->doc_type] = $doc->filename;
                }
                if (count($docList) > 0) {
                    if (isset($bcApp->id)) $bcApp->docs = (object)$docList;
                    if (isset($mgApp->id)) $mgApp->docs = (object)$docList;
                }

                if (isset($mgApp->id) &&
                    (
                        (empty($mgApp->docs->military_photo) && \App\Services\Auth::user()->studentProfile->needMilitary()) ||
                        empty($mgApp->docs->r086_photo) ||
                        empty($mgApp->docs->r063_photo) ||
                        empty($mgApp->docs->diploma_photo) ||
                        empty($mgApp->docs->atteducation_photo) ||
                        empty($mgApp->docs->kt_certificate) ||
                        empty($mgApp->docs->education_contract) ||
                        empty($mgApp->docs->education_statement) ||
                        empty($mgApp->docs->nostrificationattach_photo)
                    )
                ) {
                    $needUploadDocs = true;
                }

                if (isset($bcApp->id) &&
                    (
                        empty($bcApp->docs->r086_photo) ||
                        empty($bcApp->docs->ent_total) ||
                        empty($mgApp->docs->diploma_photo) ||
                        empty($bcApp->docs->atteducation_photo) ||
                        (empty($bcApp->docs->military_photo) && \App\Services\Auth::user()->studentProfile->needMilitary()) ||
                        empty($bcApp->docs->r063_photo) ||
                        empty($bcApp->docs->ent_certificate) ||
                        empty($bcApp->docs->education_contract) ||
                        empty($bcApp->docs->education_statement) ||

                        empty($bcApp->docs->nostrificationattach_photo)
                    )
                ) {
                    $needUploadDocs = true;
                }

                if ($needUploadDocs && ((int)date('Y',strtotime(\App\Services\Auth::user()->created_at)) > 2018)) {
                    \Session::flash('message', '<a href="' . \route('userProfile') . '#docs">' . __('Please upload missing documents to your Profile') . '</a>');
                    \Session::flash('alert-class', 'alert-warning');
                }
            }
        }

        return $next($request);
    }
}
