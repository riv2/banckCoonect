<?php

namespace App\Http\Controllers\Admin;

use App\Appeal;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class AppealController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getList()
    {
        return view('admin.appeals.list');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getListAjax(Request $request)
    {
        $searchData = Appeal::getListForAdmin(
            $request->input('search')['value'],
            $request->input('start'),
            $request->input('length'),
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

    public function review(int $id, Request $request)
    {
        $appeal = Appeal::where('id', $id)->first();

        if (empty($appeal)) {
            $this->flash_danger('Апелляция не найдена');
            return redirect()->route('adminAppealList');
        }

        $teachers = User::getTeachersForSelect();

        return view('admin.appeals.review', compact('appeal', 'teachers'));
    }

    public function reviewPost(int $id, Request $request)
    {
        /** @var Appeal $appeal */
        $appeal = Appeal::where('id', $id)->first();

        if (empty($appeal)) {
            $this->flash_danger('Апелляция не найдена');
            return redirect()->route('adminAppealList');
        }

        $resolution = !empty($request->input('approve')) ? Appeal::RESOLUTION_APPROVED : Appeal::RESOLUTION_DECLINED;

        // Expert 3
        if (!empty($appeal->expert3_id) && $appeal->expert3_id == Auth::user()->id) {
             $appeal->setExpert3Resolution($resolution, $request->input('resolution_text'));
        }
        // Expert 1
        elseif (empty($appeal->expert1_id)) {
            $appeal->setExpert1Resolution(Auth::user()->id, $resolution, $request->input('resolution_text'));
        }
        // Expert 2
        elseif (empty($appeal->expert2_id) && $appeal->expert1_id != Auth::user()->id) {
            $appeal->setExpert2Resolution(Auth::user()->id, $resolution, $request->input('resolution_text'));
        }

        // Calling expert 3
        if ($resolution == Appeal::RESOLUTION_APPROVED && empty($appeal->expert3_id) && !empty($request->input('expert3_id'))) {
            $appeal->setExpert3($request->input('expert3_id'));
        }

        // Declined
        if ($resolution == Appeal::RESOLUTION_DECLINED) {
            $appeal->setStatus(Appeal::STATUS_DECLINED, $request->input('resolution_text'));
        }
        // Approve
        else {
            $appeal->checkStatus();
        }

        return view('admin.appeals.review', compact('appeal'));
    }

    public function action(int $id, Request $request)
    {
        /** @var Appeal $appeal */
        $appeal = Appeal::where('id', $id)->first();

        if (empty($appeal)) {
            $this->flash_danger('Апелляция не найдена');
            return redirect()->route('adminAppealList');
        }

        if (empty($request->input('action'))) {
            $this->flash_danger('Выберите действие');
            return redirect()->route('adminAppealReview', ['id' => $id]);
        }

        // Add new try
        if ($request->input('action') == Appeal::RESOLUTION_ACTION_NEW_TRY) {
            $appeal->addNewTry(Auth::user()->id);
        }
        elseif ($request->input('action') == Appeal::RESOLUTION_ACTION_ADD_VALUE && !empty($request->input('value'))) {
            $appeal->addValue(Auth::user()->id, $request->input('value'));
        }

        return redirect()->route('adminAppealReview', ['id' => $id]);
    }
}
