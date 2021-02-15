<?php

namespace App\Http\Controllers;

use App\Language;
use App\QuizQuestion;
use App\Services\Auth;
use App\Services\Education;
use App\Services\StudentSpecialityCheck;
use App\Speciality;
use App\StudentEntranceTest;
use App\StudentLanguageLevel;
use App\StudentDiscipline;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Services\StepByStep;
use App\Profiles;

class SpecialityController extends Controller
{

    /**
     * Register step 5
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function select($application)
    {


        $config = $application == 'master' ? 'mg_application' : ($application == 'bachelor' ? 'bc_application' : '');
        $profile = Auth::user()->studentProfile;

        if(!$config || empty($profile) )
        {
            abort(404);
        }

        // fix current step - когда перешли сюда по ссылке
        if( Profiles::getRegisterPriority( Profiles::REGISTRATION_STEP_SPECIALITY_SELECT ) > Profiles::getRegisterPriority($profile->registration_step) )
        {
            $profile->registration_step = Profiles::REGISTRATION_STEP_SPECIALITY_SELECT . "?$application";
            $profile->save();
        }

        if( $profile->isRedirectToRegisterStep( Profiles::REGISTRATION_STEP_SPECIALITY_SELECT ) )
        {
            return $profile->getRegisterRoute( $profile->registration_step );
        }

    	if(Auth::user()->studentProfile->education_speciality_id)
        {

            Auth::user()->studentProfile->registration_step = Profiles::REGISTRATION_STEP_STUDENT_EDUCATION_LANGUAGE;
            Auth::user()->studentProfile->save();
            return redirect()->route(StepByStep::nextRouteAfter(Profiles::REGISTRATION_STEP_STUDENT_EDUCATION_LANGUAGE, $config), ['application' => $application]);
        }

        $sCurrentLocale = app()->getLocale();

    	$locale = Language::getFieldName('name', $sCurrentLocale);

    	$codeChar = $application == 'master' ? Speciality::CODE_CHAR_MASTER : ($application == 'bachelor' ? Speciality::CODE_CHAR_BACHELOR : '');
    	$application == Speciality::CODE_CHAR_MASTER;
        $specialties = Speciality
            ::where('code_char', $codeChar)
            ->where('year', /*date('Y', time())*/'2020')
            ->get();

    	return view('pages.specialityList', compact('specialties', 'application', 'locale'));

    }


    /**
     * Register step 5
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function selectSave($application, $id)
    {

        $config = $application == 'master' ? 'mg_application' : ($application == 'bachelor' ? 'bc_application' : '');

        if(!$config)
        {
            abort(404);
        }

        if(Auth::user()->studentProfile->education_speciality_id)
        {
            Auth::user()->studentProfile->registration_step = StepByStep::nextRouteAfter('specialitySelect', $config);
            Auth::user()->studentProfile->save();
            return redirect()->route(StepByStep::nextRouteAfter('specialitySelect', $config), ['application' => $application]);
        }

        $validator = Validator::make([
            'id'    => $id
        ], [
            'id'    => 'exists:specialities,id'
        ]);

        if($validator->fails())
        {
            abort(404);
        }

        Auth::user()->studentProfile->education_speciality_id = $id;
        Auth::user()->studentProfile->registration_step = Profiles::REGISTRATION_STEP_STUDENT_EDUCATION_LANGUAGE;
        Auth::user()->studentProfile->save();

        return redirect()->route(StepByStep::nextRouteAfter('specialitySelect', $config), ['application' => $application]);

    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function check()
    {
        if(count(Auth::user()->disciplines) > 0)
        {
            return redirect()->route('financesPanel');
        }

        $checkEnt = null;
        $id = Auth::user()->studentProfile->education_speciality_id;
        $speciality = Speciality::where('id', $id)->first();

        if($speciality->code_char == Speciality::CODE_CHAR_BACHELOR) {
            if ($speciality->check_ent) {
                $studentSpecialityCheck = new StudentSpecialityCheck(Auth::user()->id, $id);
                $checkEnt = $studentSpecialityCheck->checkEnt();
            }
        }

        /*if(!Auth::user()->language_english_level)
        {
            return redirect()->route('setLanguageLevel');
        }*/

        if($speciality->check_entrance_test) {
            $entranceTest = $speciality->entranceTests[0] ?? null;

            if(Auth::user()->entranceTests()->where('entrance_test_id', $entranceTest->id)->whereNull('student_entrance_test.deleted_at')->count() == 0)
            {
                $allQuestions = $entranceTest
                    ->quizeQuestions()
                    ->with([
                        'answers' => function ($query) {
                            $query->inRandomOrder();
                        }
                    ])
                    ->inRandomOrder()
                    ->limit(200)
                    ->get();

                return view('student.quize', compact('allQuestions'));
            }
        }

        return view('pages.specialityCheck', [
            'checkEnt'      => $checkEnt,
            'specialityId'  => $id
        ]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function setLanguageLevel()
    {
        if(count(Auth::user()->languageEnglishLevels) > 0)
        {
            return redirect()->route(StepByStep::nextRouteAfter('setLanguageLevel', 'bc_application'));
        }

        $englishLevelList = LanguageLevel::where('language', LanguageLevel::LANGUAGE_EN)->get();

        return view('student.setLanguageLevel', ['englishLevelList' => $englishLevelList]);
    }

    /**
     * @param Request $request
     * @param $id
     */
    public function setLanguageLevelPost(Request $request)
    {
        $validator = Validator::make([
            'language_english_level' => $request->input('language_english_level')
            ], [
            'language_english_level' => 'required|exists:language_level,id'
        ]);

        if($validator->fails())
        {
            abort(404);
        }

        StudentLanguageLevel::where('user_id', Auth::user()->id)->delete();
        StudentLanguageLevel::insert([
            'user_id' => Auth::user()->id,
            'language_level_id' => $request->input('language_english_level'),
            'created_at' => DB::raw('now()'),
            'updated_at' => DB::raw('now()')
        ]);

        return redirect()->route(StepByStep::nextRouteAfter('setLanguageLevel', 'bc_application'));
    }

    /**
     * @param Request $request
     * @param $id
     */
    public function checkPost(Request $request)
    {
        $id = Auth::user()->studentProfile->education_speciality_id;
        $speciality = Speciality::where('id', $id)->first();
        $entranceTest = $speciality->entranceTests[0] ?? null;

        $allQuestions = [];
        $pointsTotal = 0;
        foreach ($request->all() as $field => $val) {

            $field = explode('_', $field);
            if($field[0] == 'question') {

                $question = QuizQuestion
                    ::with('answers')
                    ->where('id', $field[1])
                    ->first();

                $question->gotId = $val;

                $questionPoints = 0;
                foreach ($question->answers as $answer) {
                    if(in_array($answer->id , $val) && $answer->points > 0) {
                        $questionPoints += $answer->points;
                    } elseif (in_array($answer->id , $val) && $answer->points == 0) {
                        $questionPoints = 0;
                        break;
                    }
                }
                $pointsTotal += $questionPoints;
                $allQuestions[] = $question;
            }
        }

        DB::table('student_entrance_test')
            ->leftJoin('speciality_entrance_test', 'speciality_entrance_test.entrance_test_id', '=', 'student_entrance_test.entrance_test_id')
            ->where('user_id', Auth::user()->id)
            ->where('speciality_entrance_test.speciality_id', $id)
            ->whereNull('student_entrance_test.deleted_at')
            ->update([
                'student_entrance_test.deleted_at' => DB::raw('now()'),
                'student_entrance_test.updated_at' => DB::raw('now()'),
            ]);

        StudentEntranceTest::insert([
            'user_id'           => Auth::user()->id,
            'entrance_test_id'  => $entranceTest->id,
            'points'            => $pointsTotal,
            'created_at'        => DB::raw('now()'),
            'updated_at'        => DB::raw('now()')
        ]);

        return view('student.quizeResult', [
            'pointsTotal'   => $pointsTotal,
            'allQuestions'  => $allQuestions,
            'type'          => 'entranceTest'
        ]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function confirm()
    {
        $checkEnt = null;
        $id = Auth::user()->studentProfile->education_speciality_id;
        $englishLevel = Auth::user()->language_english_level;

        if(Auth::user()->bcApplication && Auth::user()->bcApplication->check_ent) {
            $studentSpecialityCheck = new StudentSpecialityCheck(Auth::user()->id, $id);
            $checkEnt = $studentSpecialityCheck->checkEnt();
        }

        if($checkEnt === false || !$englishLevel)
        {
            abort(404);
        }

        /*$speciality = Speciality
            ::with('disciplines')
            ->whereHas('disciplines', function($query) use ($englishLevel){
                $query->where('language_level_id', $englishLevel->id);
                $query->orWhereNull('language_level_id');
            })
            ->where('id', $id)
            ->first();

        foreach ($speciality->disciplines as $item)
        {
            $studentDiscipline = new StudentDiscipline();

            $studentDiscipline->student_id = Auth::user()->id;
            $studentDiscipline->discipline_id = $item->id;

            $studentDiscipline->save();
        }*/

        return redirect()->route('financesPanel');
    }
}
	
	
  