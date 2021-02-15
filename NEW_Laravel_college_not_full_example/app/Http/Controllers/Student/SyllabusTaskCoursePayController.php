<?php
/**
 * User: Viktor Schepkin
 * Date: 2019-12-19
 * Time: 18:12
 */

namespace App\Http\Controllers\Student;

use Auth;
use App\{
    SyllabusTaskCoursePay
};
use App\Http\Controllers\Controller;
use App\Services\{Service1C,SyllabusTaskService};
use App\Validators\{
    SyllabusTaskCoursePayPayValidator,
    SyllabusTaskCoursePayPayPostValidator
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{File,Image,Log,Response,Session};

class SyllabusTaskCoursePayController extends Controller
{


    /**
     * @param Request $request
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function pay( Request $request )
    {

        // validation data
        $obValidator = SyllabusTaskCoursePayPayValidator::make( $request->all() );
        if( $obValidator->fails() || empty(Auth::user()->studentProfile) )
        {
            return redirect()->route('study')->withErrors([__('Data not found')]);
        }

        $bIsPayedCourse = false;                                                  // оплачивалась ли курсовая
        $bIsPayedCourseOk = false;                                                // если оплачивалась курсовая и был обсчет
        $bIsPayedCoursePercent = false;                                           // если оплачивалась курсовая и есть %
        $bIsDisciplineHasCourse = false;                                          // наличие курсовой у дисциплины
        $bUserHasNotTaskPoints = false;                                           // наличие оценки у юзера по дисциплине
        [$bIsPayedCourse,$bIsPayedCourseOk,$bIsPayedCoursePercent,$bIsDisciplineHasCourse,$bUserHasNotTaskPoints] = SyllabusTaskService::getCoursePayData( $request->input('discipline_id') );

        if( $bIsPayedCourse || !$bUserHasNotTaskPoints )
        {
            \Session::put('withoutBack',true);
            return redirect()->route('study')->with('flash_message',__('Your application has been accepted for consideration. Wait for a response within 24 hours.'));
        }

        // cost
        $iCost = 4000;

        return view('student.syllabustaskcoursepay.pay',[
            'cost'          => $iCost,
            'discipline_id' => $request->input('discipline_id'),
            'bIsPayedCourse'         => $bIsPayedCourse,
            'bIsDisciplineHasCourse' => $bIsDisciplineHasCourse,
            'bUserHasNotTaskPoints'  => $bUserHasNotTaskPoints,
        ]);

    }

    /**
     * @param Request $request
     * @return $this
     */
    public function payPost( Request $request )
    {

        // validation data
        $obValidator = SyllabusTaskCoursePayPayValidator::make( $request->all() );
        if( $obValidator->fails() || empty(Auth::user()->studentProfile) )
        {
            return redirect()->route('study')->withErrors([__('Data not found')]);
        }

        // cost
        $iCost = 4000;

        // test user balance
        if( $iCost > Auth::user()->balanceByDebt() )
        {
            return redirect()->route('study')->withErrors([__('Not enough funds on balance')]);
        }

        $bResponse = Service1C::pay(
            Auth::user()->studentProfile->iin,
            'БК000000828',
            $iCost
        );

        if( !empty($bResponse) )
        {

            $oSyllabusTaskCoursePay = new SyllabusTaskCoursePay();
            $oSyllabusTaskCoursePay->fill([
                'discipline_id' => $request->input('discipline_id'),
                'user_id'       => Auth::user()->id
            ]);
            $oSyllabusTaskCoursePay->save();

            \Session::put('withoutBack',true);
            return redirect()->route('study')->with('flash_message',__('Your application has been accepted for consideration. Wait for a response within 24 hours.'));

        }


        return redirect()->route('study')->withErrors([__('Error input data')]);


    }


}
