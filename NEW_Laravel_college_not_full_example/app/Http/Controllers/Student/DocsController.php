<?php

namespace App\Http\Controllers\Student;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Auth;
use App\User;
use App\StudentDiscipline;
use App\Discipline;
use Intervention\Image\Facades\Image; 
use PDF;
use App\Profiles;
use App\BcApplications;
use App\MgApplications;
use App\Speciality;
use App\DocsEnquire;
use App\Order;
use App\AdminStudentComment;
use App\OrderName;
use File;
use Illuminate\Support\Facades\Validator;
use App\Services\StudentRating;
use GoogleCloudPrint;
use App\FinanceNomenclature;
use App\Services\Service1C;

class DocsController extends Controller
{
    const DOCS_FOLDER = '/gendocuments/';
    const DOC_TYPE_TR = 'tr';
    const DOC_TYPE_ENTERED = 'enter';
    const DOC_TYPE_GCVP4 = 'gcvp4';
    const DOC_TYPE_MILITARY = 'military';
    const DOC_TYPE_GCVP21 = 'gcvp21';
    const DOC_TYPE_GCVP6 = 'gcvp6';
    const DOC_TYPE_REFUND = 'refund';

    public $folder;
    public static $transferedStudent = false;
    public static $transferedStudentInMiddleYear = false;

    function __construct()
    {  $path = public_path() . self::DOCS_FOLDER;
        if(!File::exists($path)) {
            File::makeDirectory($path, $mode = 0777, true, true);
            File::put($path.'.htaccess','Options -Indexes');
        }
    }

    public function index()
    {

    	$transcripts = DocsEnquire
            ::where('user_id', Auth::user()->id)
            ->where('doctype', self::DOC_TYPE_TR)
            ->get();

		return view('student.transcript_docs', compact('transcripts'));
    }

    public static function genTranscript($userId = null, $sector = null)
    {
        if ($userId) {
            $user = User::where('id', $userId)->first();
        } else {
            $user = Auth::user();
        }
        
        $dailyId = self::getLastDailyId(self::DOC_TYPE_TR);
    	$disciplines = Discipline::select('disciplines.*', 'specialities.name as speciality', 'students_disciplines.*')
    		->leftJoin('students_disciplines', 'students_disciplines.discipline_id', '=', 'disciplines.id')
    		->leftJoin('specialities', 
                DB::raw('(SELECT speciality_id FROM speciality_discipline 
                        WHERE discipline_id = disciplines.id ORDER BY id ASC LIMIT 1)'),
                'specialities.id')
    		->where('student_id', $user->id)
    		->get();
    	$profile = $user->studentProfile;

    	$application = MgApplications::where('user_id', $user->id)->first();
    	if(empty($application->id)) {
    		$application = BcApplications::where('user_id', $user->id)->first();
    	}


    	$errors = [];
    	if( empty($disciplines) ) {
    		$errors[] = __("You have no choisen discipline");
    	}
    	if( empty($profile) ) {
    		$errors[] = __("You have no profile data, please fill it");
    	}
    	if( empty($application) ) {
    		$errors[] = __("You need to fill out an application for a transcript");
    	}
    	if ( !empty($errors) ) {
    		return redirect()->back()->withErrors($errors);
    	}

    	foreach ($disciplines as $discipline) {
            if (is_numeric($discipline->final_result)) {
        		$discipline->test_result_string = StudentRating::getClassicString3Lang($discipline->final_result);
            } else {
                $discipline->test_result_string = null;
            }
            // if fail
            if($discipline->final_result_points < 1) {
                $discipline->final_result_points = 0;
            }
    	}
    	
    	$filename = date('YmdHis').'-'.self::DOC_TYPE_TR.'-'.substr($profile->iin, 4).'.pdf';
        $dailyFullId = self::DOC_TYPE_TR . '-' . date('md') . '-' . $dailyId;

        $speciality = Speciality::where('id', $profile->education_speciality_id)->first();

        // education language
        $locale = \App::getLocale();
        \App::setLocale('kz');
        $educationLang = __('transcript-'.$profile->education_lang) . '/ ';
        \App::setLocale('en');
        $educationLang .= __('transcript-'.$profile->education_lang) . '/ ';
        \App::setLocale('ru');
        $educationLang .= __('transcript-'.$profile->education_lang);
        \App::setLocale($locale);

        $sector = explode('||', $sector);

    	$data = [
            'disciplines'  => $disciplines, 
            'profile'      => $profile,
            'application'  => $application, 
            'filename'     => $filename,
            'dailyFullId'  => $dailyFullId,
            'speciality'   => $speciality,
            'educationLang'=> $educationLang,
            'average_gpa'  => $user->getGpaAttribute(),
            'enterDate'    => self::getEnterDate($user->id),
            'sector'       => $sector,
            'executerName' => self::executerNameShort(),
        ];
		$pdf = PDF::loadView('pdf.transcript', $data);
return $pdf->stream($filename);
		$enquire = new DocsEnquire;
		$enquire->user_id = $user->id;
		$enquire->filename = $filename;
        $enquire->doctype = self::DOC_TYPE_TR;
        $enquire->daily_id = $dailyId;
		$enquire->save();

		$pdf->save(public_path() . self::DOCS_FOLDER . $filename);
        


        return json_encode([
           'filename'    => url(self::DOCS_FOLDER . $filename),
           'dailyFullId' => $dailyFullId,
           'transcript'  => true
        ]);
        //return $pdf->download($filename);
        //return $pdf->stream($filename);
        
    }

    public static function genEntered()
    {
        return self::genGeneral(self::DOC_TYPE_ENTERED);
    }


    public static function genMilitary()
    {
        return self::genGeneral(self::DOC_TYPE_MILITARY);
    }

    public static function genGcvp4()
    {
        return self::genGeneral(self::DOC_TYPE_GCVP4);
    }

    public static function genGcvp21()
    {
        return self::genGeneral(self::DOC_TYPE_GCVP21);
    }

    public static function genGcvp6()
    {
        return self::genGeneral(self::DOC_TYPE_GCVP6);
    }

    public function check() 
    {
    	return view('pages.docsCkeck');
    }

    public function checkPost($docname)
    {
        $filename = $docname;

        $fileType = explode('-', $filename)[1];

        if($fileType == self::DOC_TYPE_TR || $fileType == self::DOC_TYPE_REFUND ) {
        	$enquire = DocsEnquire
                ::where('filename', $filename)
                ->where('doctype', $fileType)
                ->first();

            $folder = self::DOCS_FOLDER;

            return view('pages.docsCkeckResult', compact('enquire', 'folder'));
        }
        
    }

    public static function getEnterDoc()
    {
        $user = Auth::user();

        // new users
        $order = Order::getUserOrderNumber($user->id , OrderName::ORDER_CODE_ENTER);
        if(isset($order) && isset($order->number)) {
            return $order->number;
        }

        // transfered from other university
        $order = Order::getUserOrderNumber($user->id , OrderName::ORDER_CODE_TRANSFER_OTHER_UNIVER);
        if (isset($order) && isset($order->number)) {
            return $order->number;
        }
        
        //users from old DB
        $order = AdminStudentComment::select('text')
            ->where('user_id', $user->id)
            ->where('text', 'like', '%Приказ №: %')
            ->first();
        if(isset($order)) {
            $order = explode('Приказ №: ', $order->text)[1];
            $order = explode('.', $order)[0];
            return $order;
        }

        return '_____________';
    }

    public static function getEnterDate($userId = null)
    {
        if ($userId) {
            $user = User::where('id', $userId)->first();
        } else {
            $user = Auth::user();
        }

        // new users
        $order = Order::getUserOrderNumber($user->id , OrderName::ORDER_CODE_ENTER);
        if (isset($order) && isset($order->date)) {
            return strtotime($order->date);
        }
        // transfered from other university
        $order = Order::getUserOrderNumber($user->id , OrderName::ORDER_CODE_TRANSFER_OTHER_UNIVER);
        if (isset($order) && isset($order->date)) {
            // if case student was transfered after new year
            if( in_array(explode('-', $order->date)[1], [1,2,3]) ) {
                self::$transferedStudentInMiddleYear = true;
            }
            return strtotime($order->date);
        }

        
        //users from old DB 'История: 25-08-2015 - зачисление. Приказ №: '
        $order = AdminStudentComment::select('text')
            ->where('user_id', $user->id)
            ->where('text', 'like', '%Приказ №: %')
            ->first();
        if (isset($order)) {
            
            if (strrpos($order->text, 'перевод из другого университета') !== false) {
                self::$transferedStudent = true;
                $dateArray = date_parse($order->text);
                return strtotime($dateArray['day'] . '.' . $dateArray['month'] . '.' . $dateArray['year']);
            }

            $orderDate = explode('История: ', $order->text);
            if (isset($orderDate[1])) {
                $orderDate = $orderDate[1];
                $orderDate = explode(' - ', $orderDate)[0];
                return strtotime($orderDate);
            } else {
                $orderDate = explode(' - зачисление', $order->text);
                if (isset($orderDate[0])) {
                    $orderDate = trim($orderDate[0]);
                    return strtotime($orderDate);
                }
            }
        }
        return strtotime($user->studentProfile->created_at);
    }

    public static function getLastDailyId($doctype)
    {
        $lastDailyId = DocsEnquire::whereDate('created_at', DB::raw('CURDATE()'));
        if( $doctype == self::DOC_TYPE_TR || $doctype == self::DOC_TYPE_MILITARY ) {
            $lastDailyId = $lastDailyId->where('doctype', $doctype);
        }
        $lastDailyId = $lastDailyId->max('daily_id');

        $lastDailyId++;

        return sprintf("%03s", $lastDailyId);
    }

    public static function genGeneral($doctype)
    {
        $dailyId = self::getLastDailyId($doctype);
        $profile = Auth::user()->studentProfile;
        $user = Auth::user();

        $oSpeciality = Speciality::where('id', $profile->education_speciality_id)->first();
        $specialityCodeChar = $oSpeciality->code_char;
        //selecting language
        if(
            $doctype == self::DOC_TYPE_GCVP4 ||
            $doctype == self::DOC_TYPE_MILITARY ||
            $doctype == self::DOC_TYPE_MILITARY
        ) {
            $speciality = $oSpeciality->getOriginal('name_kz');
        } else {
            $speciality = $oSpeciality->getOriginal('name');
        }

        $filename = date('YmdHis').'-'.$doctype.'-'.substr($profile->iin, 4).'.pdf';
        $dailyFullId = $dailyId . '-' . $doctype . '-' . date('md');

        $enterDate = self::getEnterDate();
        
        //calculation students studying period
        $studyPeriod = 4;
        if( !empty($user->bcApplication) && ( $user->bcApplication->education == BcApplications::EDUCATION_VOCATIONAL_EDUCATION )  ) {
            $studyPeriod--;
        }
        if( $profile->category == Profiles::CATEGORY_TRANSFER) {
            $studyPeriod = $studyPeriod - $profile->course;
        }
        if( $specialityCodeChar == Speciality::CODE_CHAR_MASTER ) {
            $studyPeriod = 2;
            if( Speciality::isShaped($profile->education_speciality_id) ) {
                $studyPeriod = 1;
            }
            $studyPeriod++; // TODO require to recalculate studyPeriodLeft in correct way
        }
        if($oSpeciality->url == Speciality::URL_DESIGN) {
            $studyPeriod++;
        }
        if(self::$transferedStudentInMiddleYear && !empty($user->mgApplication)) {
            $studyPeriod--;
        }
        // TODO require to recalculate studyPeriodLeft in correct way
        $studyPeriodLeft = $studyPeriod - $profile->course;

        $data = [
            'profile'           => $profile,
            'filename'          => $filename,
            'user'              => $user,
            'enderDocId'        => self::getEnterDoc(),
            'enterDate'         => $enterDate,
            'speciality'        => $speciality,
            'dailyFullId'       => $dailyFullId,
            'studyPeriod'       => $studyPeriod,
            'studyPeriodLeft'   => $studyPeriodLeft,
            'transferedStudent' => self::$transferedStudent,
        ];
        $pdf = PDF::loadView('pdf.'.$doctype, $data);

        $enquire = new DocsEnquire;
        $enquire->user_id = Auth::user()->id;
        $enquire->filename = $filename;
        $enquire->doctype = $doctype;
        $enquire->daily_id = $dailyId;
        $enquire->save();

        $pdf->setPaper('a5');
        $pdf->save(public_path() . self::DOCS_FOLDER . $filename);
        
        $printer = env('GCP_PRINTER_ID');
        if($doctype == self::DOC_TYPE_MILITARY) {
            $printer = env('GCP_MILITARY_PRINTER_ID');
        }
        
        if( config('app.debug') == false ) {
            GoogleCloudPrint::asPdf()
                ->file(public_path() . self::DOCS_FOLDER . $filename)
                ->printer($printer)
                ->send();
        }

        return json_encode([
           'filename'    => url(self::DOCS_FOLDER . $filename),
           'dailyFullId' => $dailyFullId
        ]);
        //return $pdf->download($filename);
        //return $pdf->stream($filename);
    }


    public static function genBuyService($sServiceCode)
    {

        $oFinanceNomenclature = FinanceNomenclature::
        where('code',$sServiceCode)->
        first();

        if( !empty($oFinanceNomenclature) )
        {

            $pdf = PDF::loadView('pdf.'.$doctype, [
                'service' => $oFinanceNomenclature
            ]);

            $pdf->setPaper('a5');
            $pdf->save(public_path() . self::DOCS_FOLDER . $filename);

            $printer = env('GCP_PRINTER_ID');
            if($doctype == self::DOC_TYPE_GCVP4) {
                $printer = env('GCP_MILITARY_PRINTER_ID');
            }
            if( !config('app.debug') ) {
                GoogleCloudPrint::asPdf()
                    ->file(public_path() . self::DOCS_FOLDER . $filename)
                    ->printer($printer)
                    ->send();
            }
        }
    }
    
    public static function genRefundReference()
    {
        $user = Auth::user();
        $filename = date('YmdHis').'-'.self::DOC_TYPE_REFUND.'-'.substr($user->studentProfile->iin, 4).'.pdf';

        $data = [
            'user'       => $user,
            'enderDocId' => self::getEnterDoc(),
            'enterDate'  => self::getEnterDate(),
            'balance'    => Service1C::getBalance($user->studentProfile->iin),
            'filename'   => $filename
        ];
        $pdf = PDF::loadView('pdf.refund', $data);

        $enquire = new DocsEnquire;
        $enquire->user_id = $user->id;
        $enquire->filename = $filename;
        $enquire->doctype = self::DOC_TYPE_REFUND;
        $enquire->save();

        $pdf->save(public_path() . self::DOCS_FOLDER . $filename);

        return [
            'enquireId' => $enquire->id,
            'filename' => url(self::DOCS_FOLDER . $filename)
        ];
    }

    public static function executerNameShort()
    {
        $name = explode(' ', \App\Services\Auth::user()->name);
        
        if(isset($name[1] )) {
            $name[1] = mb_substr($name[1], 0, 1, "UTF-8") . '. ';
        }
        if(isset($name[2] )) {
            $name[2] = mb_substr($name[2], 0, 1, "UTF-8") . '. ';
        }
        return implode(' ', $name);
    }
    

    
}
	
	
  