<?php

namespace App\Http\Controllers\Admin;

use App\{
    BcApplications,
    DiscountCategoryList,
    DiscountStudent,
    DiscountTypeList,
    DiscountSemester,
    Profiles,
    ProfileDoc,
    Semester,
    StudentGpa,
    User
};
use Auth;
use App\Http\Controllers\Controller;
use App\Validators\{
    AdminDiscountRequestSetStatusValidator,
    AdminDiscountRequestSetStatusEmailValidator
};
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Log,Response};
use Mail;
use PHPUnit\Exception;

class DiscountRequestsController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getList(Request $request)
    {
        $categories = DiscountCategoryList::get();

        $gpaNewList = StudentGpa::getListForAdmin();

        return view('admin.pages.discount_request.list', compact('categories', 'gpaNewList'));
    }

    /**
     * Get list by category
     * @param Request $request
     * @param int $category_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getListByCategory(Request $request, int $category_id)
    {
        $searchData = DiscountStudent::getListForAdminByCategory(
            $category_id,
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

    /**
     * Get list Ent
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getListEnt(Request $request)
    {
        $searchData = BcApplications::getListForAdmin(
            $request->input('columns')[0]['search']['value'] ?? now()->year,
            $request->input('search')['value'],
            $request->input('start', 0),
            $request->input('length', 10),
            $request->input('order')[0]['column'] ?? 0,
            $request->input('order')[0]['dir'] ?? 'desc'
        );

        return Response::json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $searchData['recordsTotal'],
            'recordsFiltered' => $searchData['recordsFiltered'],
            'data' => $searchData['data']
        ]);
    }

    /**
     * Get list Ent
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getListCustom(Request $request)
    {
        $searchData = DiscountStudent::getCustomListForAdmin(
            $request->input('search')['value'],
            $request->input('start', 0),
            $request->input('length', 10),
            $request->input('order')[0]['column'] ?? 0,
            $request->input('order')[0]['dir'] ?? 'desc'
        );

        return Response::json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $searchData['recordsTotal'],
            'recordsFiltered' => $searchData['recordsFiltered'],
            'data' => $searchData['data']
        ]);
    }

    public function add($userID, $discountTypeID)
    {
        $item = DiscountStudent::where('user_id', $userID)->first();

        if (!$item) {
            $item = new DiscountStudent;
            $item->type_id = $discountTypeID;
            $item->user_id = $userID;
            $item->save();
        }

        return $this->edit($item->id);
    }

    public function edit($discountId, $category = null)
    {

        $item = DiscountStudent
            ::select([
                'discount_student.id',
                'discount_student.created_at',
                'discount_type_list.name_ru as name',
                'profiles.fio',
                'discount_student.status',
                'category_id',
                'discount_category_list.name as category',
                'profiles.bdate',
                'profiles.iin',
                'profiles.mobile',
                'profiles.sex',
                'profiles.education_study_form',
                'discount_type_list.discount as discountFromTable',
                'discount_student.comment',
                'profiles.user_id'
            ])
            ->leftJoin('discount_type_list', 'discount_type_list.id', '=', 'discount_student.type_id')
            ->leftJoin('discount_category_list', 'discount_category_list.id', '=', 'discount_type_list.category_id')
            ->leftJoin('profiles', 'profiles.user_id', '=', 'discount_student.user_id')
            ->where('discount_student.id', $discountId)
            ->first();

        if (isset($item->discountFromTable)) {
            $item->discount = $item->discountFromTable;
        }

        $images = ProfileDoc
            ::where('doc_type', ProfileDoc::TYPE_DISCOUNT_PROOF)
            ->where('user_id', $item->user_id)
            ->get();

        foreach ($images as $image) {
            $image->filefullpath = ProfileDoc::first()->getPathForDoc(ProfileDoc::TYPE_DISCOUNT_PROOF, $image->filename);
            $image->filefullpath .= $image->filename;
            $isNotImage = explode('.',$image->filefullpath);
            if( empty($isNotImage[1]) )
            {
                $image->filefullpath .= '-b.jpg';
            }
            $image->filefullpath = \URL::asset($image->filefullpath);
        }

        $semestersList = Semester::getSemestersList();

        return view('admin.pages.discount_request.single', compact(
            'item',
            'images',
            'category',
            'semestersList'
        ));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function setStatus(Request $request)
    {

//        $oProfiles = Profiles::
//        select([
//            'profiles.user_id',
//            'profiles.discount',
//            'discount_student.id as dsid',
//            'discount_student.user_id as userid',
//            'discount_student.status',
//        ])->
//        leftJoin('discount_student','discount_student.user_id','=','profiles.user_id')->
//        where('profiles.user_id',15013)->
//        where('profiles.discount','!=',0)->
//        whereIn('discount_student.status',['denied','canceled'])->
//        orderBy('discount_student.id','desc')->
//        toSql();

        if (empty($request->input('discount_id'))) {
            throw new \Exception('discount_id is empty');
        }

        // validation data
        $obValidator = AdminDiscountRequestSetStatusValidator::make($request->all());
        if ($obValidator->fails()) {
            $this->flash_danger('Error');
            return redirect()->route('adminDiscountRequestsEdit', ['discount_id' => $request->input('discount_id')])->withErrors($obValidator->errors());
        }

        $discountId = $request->input('discount_id');
        $newStatus = $request->input('status');

        if ($request->has('comment')) {
            $comment = $request->input('comment');
        } else {
            $comment = null;
        }
        if ($request->has('reason_refusal')) {
            $comment = $request->input('reason_refusal');
        }

        $discount = DiscountStudent::where('discount_student.id', $discountId)->first();

        if (empty($discount->date_approve)) {
            $discount->date_approve = date('Y-m-d');
        }
        $discount->status = $newStatus;
        $discount->comment = $comment;
        $discount->moderator_id = Auth::user()->id;
        $discount->save();


        // Save discount semesters
        if ($request->has('semesters')) {
            $semesters = array_map(function ($semester) use ($discount) {
                return [
                    'discount_student_id' => $discount->id,
                    'semester' => $semester,
                ];
            }, $request->input('semesters', []));
            DiscountSemester::where('discount_student_id', $discount->id)->delete();
            DiscountSemester::insert($semesters);
        }

        // Update search cache
        DiscountStudent::addToAdminSearchCache($discount);

        $discountSize = DiscountTypeList::where('id', $discount->type_id)->first()->discount;

        if ($newStatus == DiscountStudent::STATUS_APPROVED) {

            if ($request->has('discount_custom_size')) {
                $discountSize = $request->input('discount_custom_size');
            }
            $emailTemplate = 'emails.discount_request_approved';
        } elseif ($newStatus == DiscountStudent::STATUS_DENIED) {
            $emailTemplate = 'emails.discount_request_denied';
            $discountSize = null;
        } elseif ($newStatus == DiscountStudent::STATUS_CANCELED) {
            $emailTemplate = 'emails.discount_request_canceled';
            $discountSize = null;
        }

        $profile = Profiles::where('user_id', $discount->user_id)->first();
        if($discountSize === null)
        {
            $activeDiscount = DiscountStudent
                ::select(['discount_student.id as id', 'discount_type_list.discount as discount'])
                ->leftJoin('discount_type_list', 'discount_type_list.id', '=', 'discount_student.type_id')
                ->where('status', DiscountStudent::STATUS_APPROVED)
                ->where('discount_student.user_id', $profile->user_id)
                ->first();

            $discountSize = $activeDiscount->discount ?? null;
        }

        $user = User::where('users.id', $discount->user_id)->first();

        // send notification
        if ($request->has('reason_refusal')) {

            $namespace = 'App\Http\Controllers\Admin';
            $controller = app()->make($namespace . '\NotificationController');
            $res = $controller->callAction('send', [
                'request' => new Request,
                'users' => [$user->id],
                'text' => $request->input('reason_refusal')
            ]);

        }

        // validation email
        $obValidator = AdminDiscountRequestSetStatusEmailValidator::make(['email' => $user->email ?? '']);
        if (empty($obValidator->fails())) {
            Mail::send($emailTemplate,
                array(
                    'email' => $user->email,
                    'comment' => $comment
                ), function ($message) use ($user, $comment) {
                    $message->from(getcong('site_email'));
                    $message->to($user->email, ' Заявка на скидку');
                });
        }

        \Session::flash('flash_message', __('Status has been changed'));

        return redirect()->route("adminDiscountRequestsList");
    }
}
