<?php

namespace App\Http\Controllers\Admin;

use App\Order;
use App\Services\UserApplicationService;
use App\UserApplication;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use App\Models\StudentRequest\StudentRequestType;
use App\Models\StudentRequest\StudentRequestTypeSigner;
use App\EmployeesPosition;
use App\ProfileDocsType;
use App\Models\StudentRequest\StudentRequest;
use App\Models\StudentRequest\StudentRequestComment;
use App\Models\StudentRequest\StudentRequestSign;
use Auth;
use App\OrderUser;
use App\EmployeesUsersPosition;
use App\ProfileDoc;

class ApplicationController extends Controller
{

    /**
     * @param Request $request
     * @param $type
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getList(Request $request, $type)
    {
        //$list = UserApplication::with('studentProfile')->get();

        $list = StudentRequest::with('studentProfile')
                    ->select('student_requests.*', 'student_request_types.key')
                    ->leftJoin('student_request_types', 'student_request_types.id', 'student_requests.type_id')
                    ->get();

        foreach ($list as $item) {
            $item->status = StudentRequest::getStatus($item);
        }

        return view('admin.pages.applications.orders.list', ['list' => $list]);
    }

    /**
     * @param Request $request
     * @param $type
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getOne(Request $request, $type, $id)
    {
        $userApplication = StudentRequest
            ::with(['studentProfile' => function($query){
                $query->with('speciality');
            }])
            ->leftJoin('student_request_types', 'student_requests.type_id', '=', 'student_request_types.id')
            ->leftJoin('profile_docs', 'profile_docs.id', 'student_requests.doc_id')
            ->where('student_requests.id', $id)
            ->where('student_request_types.key', $type)
            ->first();
//dd($userApplication);
        
        $profileDoc = new ProfileDoc;
            $userApplication->file_src = url($profileDoc->getPathForDoc(StudentRequestType::DOCS_TYPE_PREFIX . $userApplication->key, $userApplication->filename) . $userApplication->filename . ProfileDoc::EXT);

        $orderList = Order
            ::with('orderName')
            ->whereIn('order_action_id', [1,2,3,9])
            ->get();
//dd($userApplication);
        return view('admin.pages.applications.orders.edit', [
            'application' => $userApplication,
            'orderList'  => $orderList,
            'type'       => $type,
            'id'         => $id,
        ]);
    }


    /**
     * @param Request $request
     * @param $type
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajaxCommentList(Request $request, $type)
    {

        $validator = Validator::make($request->all(), [
            'request_id' => 'required|exists:student_requests,id'
        ]);

        if($validator->fails()) {
            return [
                'status' => false,
                'message' => $validator->messages()
            ];
        }

        $list = StudentRequestComment
                    ::select('student_request_comments.text', 'student_request_comments.id', 'student_request_comments.for_student', 'student_request_comments.created_at', 'users.name')
                    ->leftJoin('users', 'users.id', '=', 'student_request_comments.user_id')
                    ->where('student_request_comments.request_id', $request->input('request_id') )
                    ->get();

        return [
            'status' => true,
            'list'   => $list,
        ];
    }

    /**
     * @param Request $request
     * @param $type
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajaxGetSignList(Request $request, $type)
    {
        $validator = Validator::make($request->all(), [
            'request_id' => 'required|exists:student_requests,id'
        ]);

        if($validator->fails()) {
            return [
                'status' => false,
                'message' => $validator->messages()
            ];
        }

        $type = StudentRequestType::where('key', $type)->first();

        $listSigns = StudentRequestSign
                    ::select('employees_positions.name as position', 'users.name', 'employees_positions.name as position', 'employees_positions.id as position_id', 'users.id as user_id')
                    ->leftJoin('users', 'users.id', '=', 'student_request_signs.user_id')
                    ->leftJoin('employees_positions', 'employees_positions.id', '=', 'student_request_signs.position_id')
                    ->where('request_id', $request->input('request_id'))
                    ->get();

        $listTypeSign = StudentRequestTypeSigner
                    ::select('employees_positions.name as position', 'employees_positions.id as position_id')
                    ->leftJoin('employees_positions', 'employees_positions.id', '=', 'student_request_type_signers.position_id')
                    
                    ->where('type_id', $type->id)
                    ->get();

        $user = Auth::user();
        $list = [];
        $allSigned = true;
        $currentUserSign = false;
        foreach($listTypeSign as $typeSign) {
            $item = new \stdClass();
            $item->position = $typeSign->position;
            foreach ($listSigns as $sign) {
                if($typeSign->position_id == $sign->position_id) {
                    $item->name = $sign->name;
                    $item->position = $sign->position;
                    $item->signed = true;
                }
                if($sign->user_id == $user->id) {
                    $currentUserSign = true;
                }
            }
            if(!isset($item->signed)) {
                $allSigned = false;
            }
            $list[] = $item;
        }

        $requestStatus = StudentRequest::where('id', $request->input('request_id'))->first();
        if( isset($requestStatus->user_id_who_declined) ) {
            $currentUserSign = true;
        }

        return [
            'status'          => true,
            'list'            => $list,
            'allSigned'       => $allSigned,
            'currentUserSign' => $currentUserSign,
        ];
    }

    /**
     * @param Request $request
     * @param $type
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajaxAddSign(Request $request, $type)
    {
        $validator = Validator::make($request->all(), [
            'request_id' => 'required|exists:student_requests,id',
            'user_id'    => 'required',
        ]);

        if($validator->fails()) {
            return [
                'status' => false,
                'message' => $validator->messages()
            ];
        }

        $position = EmployeesPosition::
                        leftJoin('employees_positions', 'employees_positions.position_id', '=', 'employees_positions.id')
                        ->where('user_id', $requiest->input('user_id') )->first();

        if(empty($position)) {
            return [
                'status' => false,
                'message' => 'Пользователь не имеет позиции',
            ];
        }

        $sign = new StudentRequestSign;
        $sign->user_id = $requiest->input('user_id');
        $sign->request_id = $requiest->input('request_id');
        $sign->position_id = $position->id;
        $sign->save();

        return [
            'status' => true,
        ];

    }

    

    /**
     * @param Request $request
     * @param $type
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajaxCommentAdd(Request $request, $type)
    {
        $validator = Validator::make($request->all(), [
            'request_id' => 'required|exists:student_requests,id',
            'text'       => 'required',
        ]);

        if($validator->fails()) {
            return [
                'status' => false,
                'message' => $validator->messages()
            ];
        }

        $comment = new StudentRequestComment;
        $comment->request_id = $request->input('request_id');
        $comment->user_id = Auth::user()->id;
        $comment->text = $request->input('text');
        $comment->for_student = $request->input('forStudent');
        $comment->save();


        return [
            'status' => true,
        ];

    }

    
    /**
     * @param Request $request
     * @param $type
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajaxSetOrder(Request $request, $type)
    {
        $validator = Validator::make($request->all(), [
            'request_id' => 'required|exists:student_requests,id',
            'order_id' => 'required|exists:orders,id'
        ]);

        if($validator->fails()) {
            return Response::json([
                'status' => false,
                'message' => $validator->messages()
            ]);
        }

        $studentRequest = StudentRequest::where('id', $request->input('request_id'))->first();

        $order = new OrderUser;
        $order->user_id = $studentRequest->user_id;
        $order->order_id = $request->input('order_id');
        $order->save();

        return [
            'status' => true,
        ];


    }

    /**
     * @param Request $request
     * @param $type
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajaxConfirm(Request $request, $type)
    {
        $validator = Validator::make($request->all(), [
            'request_id' => 'required|exists:student_requests,id'
        ]);

        if($validator->fails()) {
            return [
                'status' => false,
                'message' => $validator->messages()
            ];
        }

        $position = EmployeesUsersPosition
                        ::leftJoin('employees_positions', 'employees_positions.id', '=', 'employees_users_positions.position_id')
                        ->where('user_id', Auth::user()->id)
                        ->first();

        if(empty($position)) {
            return [
                'status' => false,
                'message' => 'Данное заявление не требует вашей подписи'
            ];
        }

        $sign = new StudentRequestSign;
        $sign->request_id = $request->input('request_id');
        $sign->user_id = Auth::user()->id;
        $sign->position_id = $position->id;
        $sign->save();

        return [
            'status' => true,
        ];
    }

    /**
     * @param Request $request
     * @param $type
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajaxDecline(Request $request, $type)
    {
        $validator = Validator::make($request->all(), [
            'request_id' => 'required|exists:student_requests,id'
        ]);

        if($validator->fails())
        {
            return [
                'status' => false,
                'message' => $validator->messages()
            ];
        }

        $studentRequest = StudentRequest::where('id', $request->input('request_id'))->first();
        $studentRequest->user_id_who_declined = Auth::user()->id;
        $studentRequest->save();

        return [
            'status' => true,
        ];
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getTypesList()
    {
        $list = StudentRequestType::get();

        return view('admin.pages.applications.types.list', ['list' => $list]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function typeAdd()
    {
        $positions = EmployeesPosition::get();
        return view('admin.pages.applications.types.addEdit',['positions' => $positions]);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function typeEdit($id)
    {
        $item = StudentRequestType::where('id', $id)->first();

        $positions = EmployeesPosition::get();

        $signers = [];
        $signersObj = StudentRequestTypeSigner::select('position_id')->where('type_id', $id)->get();
        foreach ($signersObj as $signer) {
            $signers[] = $signer->position_id;
        }

        return view('admin.pages.applications.types.addEdit', [
            'item'      => $item, 
            'positions' => $positions,
            'signers'   => $signers,
        ]);
    }

    /**
     * @param $id
     * @return 
     */
    public function typeDelete($id)
    {
        $type = StudentRequestType::where('id', $id)->first();
        ProfileDocsType::where('type', StudentRequestType::DOCS_TYPE_PREFIX . $type->key)->delete();
        $type->delete();

        return redirect()->route('adminApplicationTypeList');
    }

    /**
     * @param Request $request
     * @return 
     */
    public function typeAddEdit(Request $request)
    {
        $data =  \Input::except(array('_token')) ;
        
        $inputs = $request->all();
        
        $rule = [];
        if(!empty($inputs['id'])){
            $rule['key'] = 'required';
        } else {
            $rule['key'] = 'required|unique:student_request_types,key';
        }
        
        $validator = \Validator::make($data, $rule);
 
        if ($validator->fails()){
            return redirect()->back()->withErrors($validator->messages());
        }

        if(!empty($inputs['id'])){
            $type = StudentRequestType::findOrFail($inputs['id']);
            $docType = ProfileDocsType::where('type', StudentRequestType::DOCS_TYPE_PREFIX . $type->key)->first();
            if($docType == null) {
                $docType = new ProfileDocsType;
            }
        } else{
            $type = new StudentRequestType;
            $docType = new ProfileDocsType;
        }
        foreach ($inputs as $key => $input) {
            if ($key == 'id' || $key == '_token' || $key == 'template_doc' || $key == 'positions' ) {
                continue;
            }
            $type->{$key} = $input;
        }   
        
        $docType->type = StudentRequestType::DOCS_TYPE_PREFIX . $inputs['key'];
        $doc = $request->file('template_doc');

        if(isset($doc)) {
            $fileName = str_random(3) . '-' . $doc->getClientOriginalName();
            $doc->move(public_path('images/uploads/'.$docType->type), $fileName);
            $type->template_doc = $fileName;
        }

        $docType->hidden = 1;
        $docType->save();

        $type->save();


        if( isset($inputs['positions']) && !empty($inputs['id']) ) { 
            $currentPositions = StudentRequestTypeSigner::where('type_id', $type->id )->get();

            foreach ($currentPositions as $position) {
                if(in_array($position->id, $inputs['positions'])) {
                    // removind existing position from array to not dublicate in DB
                    $inputs['positions'] = array_diff( $inputs['positions'], [$position->id] );
                } else {
                    $position->delete();
                }
            }
        }
        if( isset($inputs['positions']) ) {
            foreach ($inputs['positions'] as $position) {
                $signer = new StudentRequestTypeSigner;
                $signer->type_id = $type->id;
                $signer->position_id = $position;
                $signer->save();
            }
        }


        if(!empty($inputs['id'])){
            \Session::flash('flash_message', __('Changes Saved'));
        }else{
            \Session::flash('flash_message', __('Added'));
        }

        return redirect()->route('adminApplicationTypeList');
    }

    
}
