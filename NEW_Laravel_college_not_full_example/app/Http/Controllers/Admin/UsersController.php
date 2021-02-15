<?php

namespace App\Http\Controllers\Admin;

use App\Role;
use App\Services\SearchCache;
use Auth;
use App\User;
use App\Place;
use App\PlaceUser;
use Carbon\Carbon;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Session;
use Intervention\Image\Facades\Image; 
use Illuminate\Support\Facades\DB;
use Mail;
use App\ProfileDoc;
use App\BcApplications;
use App\MgApplications;
use App\Profiles;
use App\ProfileDocsType;

class UsersController extends MainAdminController
{
	
    public function userslist()
    {
        return view('admin.pages.users');
    }

    /**
     * Ajax answer
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getListAjax(Request $request)
    {
        $searchData = User::getListForAdmin(
            $request->input('search')['value'],
            $request->input('start', 0),
            $request->input('length', 10),
            $request->input('order')[0]['column'] ?? 0,
            $request->input('order')[0]['dir'] ?? 'asc'
        );

        return Response::json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $searchData['recordsTotal'],
            'recordsFiltered' => $searchData['recordsFiltered'],
            'data' => $searchData['data']
        ]);
    }

    public function addeditUser()
    { 
        $roleList = Role::get();

        return view('admin.pages.addeditUser', ['roleList' =>$roleList]);
    }
    
    public function addnew(Request $request)
    { 
    	
    	$data =  \Input::except(array('_token')) ;
	    
	    $inputs = $request->all();
	    
	    $rule=array(
		        'name' => 'required',
		        'email' => 'required|email|max:75|unique:users,id',
		        'password' => 'nullable|min:6|max:15',
		        'image_icon' => 'mimes:jpg,jpeg,gif,png' 
		   		 );
	    
	   	 $validator = \Validator::make($data,$rule);
 
        if ($validator->fails())
        {
                return redirect()->back()->withErrors($validator->messages());
        } 
	      
		if(!empty($inputs['id'])){
           
            $user = User::findOrFail($inputs['id']);

        }else{

            $user = new User;

        }
		
		 
		//User image
		$user_image = $request->file('image_icon');
		 
        if($user_image){
            
            \File::delete(public_path() .'/upload/members/'.$user->image_icon.'-b.jpg');
		    \File::delete(public_path() .'/upload/members/'.$user->image_icon.'-s.jpg');
            
            $tmpFilePath = 'upload/members/';

            $hardPath =  str_slug($inputs['name'], '-').'-'.md5(time());
			
            $img = Image::make($user_image);

            $img->fit(376, 250)->save($tmpFilePath.$hardPath.'-b.jpg');
            $img->fit(80, 80)->save($tmpFilePath.$hardPath. '-s.jpg');

            $user->image_icon = $hardPath;
             
        }

		$user->name = $inputs['name'];		 
		$user->email = $inputs['email'];

		if($user->studentProfile) {
            $user->studentProfile->mobile = $inputs['phone'] ?? null;
            $user->studentProfile->save();
        }

		$user->about = $inputs['about'];
		$user->facebook = $inputs['facebook'];
		$user->insta = $inputs['insta'];
		//$user->group_id = Auth::User()->group_id;
		
		if($inputs['password'])
		{
			$user->password= bcrypt($inputs['password']); 
		}
		 
	    $user->save();
		$user->teacherDisciplines()->sync($request->input('disciplines'));

		if($request->input('roles'))
        {
            $roleList = [];
            foreach ($request->input('roles') as $k => $item)
            {
                if($item)
                {
                    $roleList[] = $k;
                }
            }

            $user->roles()->sync($roleList);
        }
	    
	    /*$placeUser = PlaceUser::where('group_id', '=', Auth::User()->group_id);
	    
	    //remove all rules
	    $placeUser->where('user_id', '=', $user->id)->delete();
	    
	    //add rules depend on user type
	    if($inputs['usertype'] == 'Master') {
		    
		    $placeList = $inputs['places'];
		    
	    } elseif($inputs['usertype'] == 'SuperMaster') {
		    
		    $placeList = Place::select('id')->where('group_id', '=', Auth::User()->group_id)->get();
		    
		    forEach($placeList AS $item) $items[] = $item->id;
		    
			$placeList = $items;
	    }
	    
	    //print_r($placeUser);
	    //adding places 
	    forEach($placeList AS $place) {
		    $placeArray[] = ['user_id' => $user->id, 'place_id' => $place, 'group_id' => Auth::User()->group_id];
		}
	    $placeUser->insert($placeArray);
	    */
		
		if(!empty($inputs['id'])){

            \Session::flash('flash_message', 'Changes Saved');

        }else{
	        
            \Session::flash('flash_message', 'Added');
            
        }
        $user->refreshSearchAdminMatriculants();

        return redirect()->route('users');
         
    }     
    
    public function editUser($id)    
    {     
		$user = User::with('teacherDisciplines')->findOrFail($id);
        $roleList = Role::get();

        if (!$user->phone){
            $user->phone = $user->studentProfile->mobile ?? '';
        }

		return view('admin.pages.addeditUser',compact('user', 'roleList'));
        
    }	 
    
    public function delete($id)
    {
    	$user = User::findOrFail($id);
        
		\File::delete(public_path() .'/upload/members/'.$user->image_icon.'-b.jpg');
		\File::delete(public_path() .'/upload/members/'.$user->image_icon.'-s.jpg');
			
		$user->delete();
		
        \Session::flash('flash_message', 'Deleted');

        return redirect()->back();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteAjax(Request $request) {
        $userIdList = $request->input('user_list');

        if($userIdList)
        {
            $userList = User::whereIn('id', $userIdList)->get();

            foreach ($userList as $user)
            {
                $user->delete();
                $user->refreshSearchAdminMatriculants();
            }

            Mail::send('emails.user_delete_report',
            array(
                'userIdList'  => implode(', ', $userIdList),
                'date'        => date('d-m-Y H:i'),
                'executor'    => Auth::user()->id
            ), function ($message) {
                $message->from(getcong('site_email'), getcong('site_name'));
                $message->to( explode(',',env('MAIL_FOR_USER_DELETE_REPORT')) )->subject('Отчет о удаление пользователя/ей');
            });
        }

        return Response::json();
    }


    /**
     * @param Request $request
     * @return array
     */
    public function adminDocsUploadPost($id = null, Request $request)
    {
        if ($id === null) {
            $id = Auth::user()->id;
        }
        
        $inputs = $request->all();

        if ($inputs['type'] == 'bc'){
            $application = BcApplications::where('user_id', $id)->first();
        } else {
            $application = MgApplications::where('user_id', $id)->first();
        }

        if ( empty($id) || empty($application) ){
            return redirect()->back()->withErrors([__("Data not found")]);
        }

        /*if ($inputs['doc_type'] == ProfileDoc::) {
            $application->syncResidenceRegistration($request->file('residenceregistration', null));
        }*/

        $docsChange = false;
        $singleFile = true;
        if(count($request->file('files')) > 1) {
            $singleFile = false;
        }
        $user = User::findOrFail($id);
        $profile = $user->studentProfile;

        if ($inputs['doc_type'] == ProfileDoc::TYPE_MILITARY) {
            $docsChange = isset($profile->doc_military);
            //$application->syncMilitary();
            $docType = ProfileDoc::TYPE_MILITARY;
        }

        if ($inputs['doc_type'] == ProfileDoc::TYPE_R086) {
            $docsChange = isset($profile->doc_r086);
            //$application->syncR086();
            $docType = ProfileDoc::TYPE_R086;
        }

        if ($inputs['doc_type'] == ProfileDoc::TYPE_R063) {
            $docsChange = isset($profile->doc_r063);
            //$application->syncR063();
            $docType = ProfileDoc::TYPE_R063;
        }

        if ($inputs['doc_type'] == ProfileDoc::TYPE_DIPLOMA) {
            $docsChange = isset($profile->diploma_photo);
            $docType = ProfileDoc::TYPE_DIPLOMA;
        }

        if ($inputs['doc_type'] == ProfileDoc::TYPE_ATTEDUCATION) {
            $docsChange = isset($profile->doc_atteducation);
            //$application->syncAttEducation();
            $docType = ProfileDoc::TYPE_ATTEDUCATION;
        }

        if ($inputs['doc_type'] == ProfileDoc::TYPE_NOSTRIFICATION) {
            $docsChange = isset($profile->doc_nostrification);
            //$application->syncNostrificationAttach();
            $docType = ProfileDoc::TYPE_NOSTRIFICATION;
        }

        if ($inputs['doc_type'] == ProfileDoc::TYPE_EDUCATION_STATEMENT) {
            $docsChange = isset($profile->education_statement);
            $docType = ProfileDoc::TYPE_EDUCATION_STATEMENT;
        }

        if ($inputs['doc_type'] == ProfileDoc::TYPE_KT_CERTIFICATE) {
            $docsChange = isset($profile->kt_certificate);
            $docType = ProfileDoc::TYPE_KT_CERTIFICATE;
        }

        if ($inputs['doc_type'] == ProfileDoc::TYPE_WORK_BOOK) {
            $docsChange = isset($profile->doc_work_book);
            $docType = ProfileDoc::TYPE_WORK_BOOK;
        }

        // when we didn't find value from constans, use DB, in future better remove find by const
        if (!isset($docType)) {
            $type = ProfileDocsType::where('type', $inputs['doc_type'])->first();
            $docType = $type->type;
        }
        

        if (isset($docType)) {
            foreach ($request->file('files') as $file) {
                ProfileDoc::saveDocument($docType, $file, null, $singleFile, $id);
            }
        }

        if ($docsChange){
            $profile->docs_status = Profiles::DOCS_STATUS_EDIT;
            $profile->save();
        }

        $application->save();

        return ['status' => 'success'];
        //return redirect()->back();
    }

    public function adminGetUserDocsList($id)
    {
        return ProfileDoc::getUserDocsList($id);
    }

    public function adminDocsSetStatus($id, Request $request)
    {
        $inputs = $request->all();

        $doc = ProfileDoc::findOrFail($inputs['docId']);

        if ($inputs['status'] == 'hide') {
            $doc->last = 0;
        } else {
            $doc->status = $inputs['status'];
        }
        $doc->delivered = $inputs['delivery'];
        $doc->save();
        return ['status' => 'success'];
    }

}
