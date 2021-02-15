<?php

namespace App\Http\Controllers;

use App\Services\Auth;
use App;
use App\User;
use App\Settings;
use App\Promo;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

use Illuminate\Support\Facades\Redirect;
use Mail;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Profiles;

class PromoController extends Controller
{


    public function promo()
    { 
        return Redirect::to('http://miras.edu.kz/landing/');
        //return view('promo.main'); 
    }

    public function imageUpload($image, $fileExt, $tmpFilePath)
    {
        $fileName =  str_slug($image->getClientOriginalName(), '-').'-'.md5(str_random(7));
        $fileExtName = $fileName.$fileExt;
        $image->move(public_path($tmpFilePath), $fileName);
        if( \File::mimeType( public_path($tmpFilePath.$fileName) ) == 'application/octet-stream' ) {
            shell_exec('tifig  -v -p '.$image->getPathName().' ' .public_path($tmpFilePath).$fileExtName);
        } else {
            rename(public_path($tmpFilePath).$fileName, public_path($tmpFilePath).$fileExtName);
        }
        return $fileExtName;
    }


    public function promoIDPost(Request $request)
    {
        $data =  \Input::except(array('_token')) ;
        
        $inputs = $request->all();

        $tmpFilePath = 'images/uploads/promo/';
        
        $frontFile = HelpController::imageIDUpload($request->file('front'), '-f-b.jpg', $tmpFilePath);
        $backFile = HelpController::imageIDUpload($request->file('back'), '-b-b.jpg', $tmpFilePath);
        


        $type = 'kaz.id.*';
        $SIDFront = shell_exec('php '.__DIR__.'/SmartID/SmartID.php '.public_path($tmpFilePath).$frontFile.' '.__DIR__.'/SmartID/bundle_kaz_mrz_server.zip '.$type.'');
        $SIDFront = json_decode($SIDFront);

        $type = 'kaz.id.*';
        $SIDBack = shell_exec('php '.__DIR__.'/SmartID/SmartID.php '.public_path($tmpFilePath).$backFile.' '.__DIR__.'/SmartID/bundle_kaz_mrz_server.zip '.$type.'');
        $SIDBack = json_decode($SIDBack);

        if(!empty($SIDFront->str)) {
            foreach($SIDFront->str AS $key => $val) {
                $SID[$key] = $val;
            }
        }

        if(!empty($SIDBack->str)) {
            foreach($SIDBack->str AS $key => $val) {
                $SID[$key] = $val;
            }
        }
        $SID['init'] = 1;
        $SID = (object) $SID;
        //print_r($SID);

        if(isset($SID->surname)) $SID->fio = $SID->surname;
        if(isset($SID->name)) $SID->fio .= ' '.$SID->name;
        if(isset($SID->patronymic)) $SID->fio .= ' '.$SID->patronymic;
        
        if(isset($SID->number_mrz) and !isset($SID->number)) $SID->number = $SID->number_mrz;
        
        //did we get iin?
        if(empty($SID->inn) or !is_numeric($SID->inn)) {
            \File::delete(public_path($tmpFilePath.$frontFile));
            \File::delete(public_path($tmpFilePath.$backFile));
            return redirect()->route('promo')->withErrors(__("Error reading data from a file, try to take a picture again with better lighting"));
        }

        //checking if user exist
        $promo = Promo::where('iin', '=', $SID->inn)->first();
        if( !empty($promo) ) {
            \File::delete(public_path($tmpFilePath.$frontFile));
            \File::delete(public_path($tmpFilePath.$backFile));
            return redirect()->route('promo')->withErrors(__("We already have such a document in the database"));
        }
        
        $promo = new Promo;

        $promo->iin = $SID->inn;
        if (isset($SID->fio)) $promo->fio = $SID->fio;
        if (isset($SID->birth_date)) $promo->bdate = strtotime($SID->birth_date);
        if (isset($SID->number)) $promo->docnumber = $SID->number;
        if (isset($SID->issue_authority)) $promo->issuing = $SID->issue_authority;
        if (isset($SID->issue_date)) $promo->issuedate = strtotime($SID->issue_date);
        if (isset($SID->expiry_date)) $promo->expire_date = strtotime($SID->expiry_date);
        if (isset($SID->gender_mrz)) if( $SID->gender_mrz == "M") $promo->sex = 1; else $promo->sex = 0;
        if (isset($inputs['phone'])) $promo->phone = $inputs['phone'];
        $promo->front_id_photo = $frontFile;
        $promo->back_id_photo = $backFile;

        $promo->save();


        \Session::flash('flash_message', $promo->fio.' '.__("Thank! Your document").' '.$promo->iin.' '.__("accepted").'.');
        return redirect()->route('promo');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function promoContact(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'      => 'required',
            'callback'  => 'required'
        ]);

        if($validator->fails())
        {
            \Session::flash('flash_message', __("You must fill in all fields"));
            return redirect()->back();
        }
        
        $promo = new Promo();
        $promo->fio = $request->input('name');
        $promo->phone = $request->input('callback');
        $promo->save();

        $mail = env('MAIL_FOR_PROMO', '');
        $mailAuthor = env('MAIL_AUTHOR', '');

        if(!$mail || !$mailAuthor)
        {
            Log::warning('MAIL_FOR_PROMO or MAIL_AUTHOR not set');
        }
        else
        {
            try {
                Mail::send('emails.contact',
                    array(
                        'name' => $request->input('name'),
                        'phone' => $request->input('callback'),
                    ), function ($message) use ($mailAuthor, $mail) {
                        $message->from($mailAuthor, getcong('site_name') . ' promo');
                        $message->to($mail, getcong('site_name'))->subject(getcong('site_name') . ' promo');
                    });
            }
            catch (\Exception $e)
            {
                Log::error($e);
            }
        }

        \Session::flash('flash_message', __('Thank! The application has been sent.'));
        return redirect()->route('promo');
    }


    

}