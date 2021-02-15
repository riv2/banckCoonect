<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\{MgApplications,ProfileDoc,Profiles,City};
use App\Services\{Auth,RegistrationHelper,Service1C,StepByStep};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB,Log};

class MgApplicationController extends Controller
{
    private $parts = [
        'address',
        'kt_certificate',
        'publications',
        'education'
    ];

    /**
     * @param $currentPart
     * @param $profileMgApplicationController
     * @return bool|string
     */
    private function nextPart($currentPart, $profile)
    {
        $result = false;

        if($currentPart == 'address')
        {
            $result = 'kt_certificate';
        }

        if($currentPart == 'kt_certificate')
        {
            $result = 'publications';
        }

        if($currentPart == 'publications')
        {
            $result = 'education';
        }

        if( $currentPart == $this->parts[ count($this->parts) - 1 ] )
        {
            //$result = 'finish';
        }

        return $result;
    }

    /**
     * @param $part
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function partForm($part)
    {
        $profile = Profiles::where('user_id', '=', Auth::user()->id)->first();

        if($part == 'finish')
        {
            /*
            $profile->registration_step = 'finish';
            $profile->education_status = Profiles::EDUCATION_STATUS_MATRICULANT;
            $profile->save();
            Auth::user()->setRole('client');
            Auth::user()->unsetRole('guest');
            Auth::user()->refreshSearchAdminMatriculants();
            Auth::user()->updateGuestSearchCache();
            Service1C::registration($profile->iin, $profile->fio, $profile->sex, $profile->bdate);
            return redirect()->route('study');
            */
        }

        if($part == Profiles::REGISTRATION_STEP_AGITATOR)
        {

            return redirect()->route('profileAddAgitator');

        }

        if(!in_array($part, $this->parts))
        {
            abort(404);
        }

        RegistrationHelper::setRegistrationStep( Profiles::REGISTRATION_TYPE_CLIENT, $part );

        if($part == 'military' && !$profile->needMilitary())
        {
            abort(404);
        }

        $mgApplication = MgApplications::where('user_id', Auth::user()->id)->first();
        if( !$mgApplication && $part == $this->parts[0] )
        {
            $mgApplication = new MgApplications();
        }
        elseif($mgApplication)
        {
            $mgApplication->part = $part;
            $mgApplication->save();
        }
        elseif($part != $this->parts[0] && !$mgApplication)
        {
            return redirect()->route('mgApplicationPart', ['part' => $this->parts[0]]);
        }

        $viewParams = [
            'profile'       => $profile,
            'mgApplication' => $mgApplication,
            'part'   => $part
        ];

        if( in_array($part, ['address', 'education']))
        {
            $viewParams['regions'] = DB::table('regions')->orderBy('id')->orderBy('name')->get();
            $viewParams['cities'] = DB::table('cities')->where('hidden', false)->orderBy('id')->orderBy('name')->get();
            $viewParams['countries'] = DB::table('country_list')->orderBy('id')->orderBy('name')->get();
        }

        return view('student.mg_application.' . $part, $viewParams);
    }

    /**
     * @param Request $request
     * @param $part
     * @return \Illuminate\Http\RedirectResponse
     */
    public function partPost(Request $request, $part)
    {
        if(!in_array($part, $this->parts))
        {
            abort(404);
        }

        $input = $request->all();

        if($part == 'address') {
            $city = City::where('name', $request->input('city'))->first();
            if(!$city) {
                $city = new City;
                $city->name = $input['city'];
                $city->hidden = true;
                $city->save();
            }
            $input['city_id'] = $city->id;
        }

        $profile = Profiles::where('user_id', '=', Auth::user()->id)->first();

        if($part == 'military' && !$profile->needMilitary())
        {
            abort(404);
        }

        $application = MgApplications::where('user_id', Auth::user()->id)->first();
        if(!$application)
        {
            $application = new MgApplications();
        }

        $application->user_id = Auth::user()->id;
        $application->fill($input);
        $application->citizenship_id = Auth::user()->defaultCitizenshipId();

        if($part == 'education') {
            if($request->file('diploma_photo', null) && $request->file('diploma_photo', null)) {
                ProfileDoc::saveDocument(ProfileDoc::TYPE_DIPLOMA, $request->file('diploma_photo', null));
            }
            if($request->file('atteducation', null)) {
                $application->syncAttEducation($request->file('atteducation', null), 'front');
                $application->syncAttEducation($request->file('atteducation_back', null), 'back');
            }
            if($request->file('nostrificationattach', null)) {
                $application->syncNostrificationAttach($request->file('nostrificationattach', null));
            }
            if($request->file('nostrificationattach_back', null)) {
                $application->syncNostrificationAttachBack($request->file('nostrificationattach_back', null));
            }
        }

        if($part == 'publications') {
            $application->syncWorkBook($request->file('work_book', null));
        }

        $application->part = ($part != $this->parts[count($this->parts) - 1]) ? $part : 'finish';
        $application->save();

        if($application->part == 'finish')
        {
            /*
            Auth::user()->setRole('client');
            Auth::user()->unsetRole('guest');
            Auth::user()->updateGuestSearchCache();
            */
        }

        if( $request->has('with_honors') )
        {
            $profile->with_honors = Profiles::EDUCATION_WITH_HONORS_ACTIVE;
        }
        if( $request->has('is_transfer') )
        {
            $profile->is_transfer = Profiles::STUDENT_TRANSFER_ACTIVE;
            if( $request->has('transfer_course') )
            {
                $profile->transfer_course = $request->input('transfer_course');
            }
            if( $request->has('transfer_study_form') )
            {
                $profile->transfer_study_form = $request->input('transfer_study_form');
            }
            if( $request->has('transfer_specialty') )
            {
                $profile->transfer_specialty = $request->input('transfer_specialty');
            }
            if( $request->has('transfer_university') )
            {
                $profile->transfer_university = $request->input('transfer_university');
            }
            $profile->category = Profiles::CATEGORY_TRANSFER;
        }

        if($part == $this->parts[count($this->parts) - 1])
        {
            /*
            $profile->registration_step = 'finish';
            $profile->education_status = Profiles::EDUCATION_STATUS_MATRICULANT;
            $profile->save();
            Auth::user()->setRole('client');
            Auth::user()->unsetRole('guest');
            Auth::user()->refreshSearchAdminMatriculants();
            Auth::user()->updateGuestSearchCache();
            Service1C::registration($profile->iin, $profile->fio, $profile->sex, $profile->bdate);
            */

            $profile->education_status = Profiles::EDUCATION_STATUS_MATRICULANT;
            $profile->save();

            RegistrationHelper::setRegistrationStep( Profiles::REGISTRATION_TYPE_CLIENT, Profiles::REGISTRATION_STEP_AGITATOR );

            return redirect()->route('profileAddAgitator');

        }

        $nextPart = $this->nextPart($part, $profile);

        if($nextPart)
        {
            return redirect()->route('mgApplicationPart', ['part' => $nextPart]);
        }
        else
        {
            return redirect()->route('profileAddAgitator');
            //return redirect()->route(StepByStep::nextRouteAfter('mgApplication', 'mg_application'));
        }
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function actualPage()
    {
        $application = MgApplications::where('user_id', Auth::user()->id)->first();
        $result = '';

        if(isset($application->part) && $application->part)
        {
            $result = $application->part;
        }
        else
        {
            $result = $this->parts[0];
        }

        return redirect()->route('mgApplicationPart', ['part' => $result]);
    }
}
