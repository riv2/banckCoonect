<?php

namespace App\Http\Controllers\Student;

use App\Promotion;
use App\PromotionUser;
use App\PromotionUserWork;
use App\Services\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class PromotionController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getList(Request $request)
    {
        $promotionList = Promotion::where('status', Promotion::STATUS_ACTIVE)->get();

        $showId = $request->input('show', 0);

        return view('student.promotion.list', [
            'promotionList' => $promotionList,
            'showId' => $showId
        ]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendRequest(Request $request, $id)
    {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|exists:promotions,id',
            //'pension_report_file' => 'required',
            //'work_certificate_file' => 'required',
        ]);

        if($validator->fails() ||
            PromotionUser::where('promotion_id', $id)->where('user_id', Auth::user()->id)->count() > 0 )
        {
            abort(404);
        }

        $promotion = Promotion::where('id', $id)->first();

        $promotionUser = new PromotionUser();
        $promotionUser->user_id = Auth::user()->id;
        $promotionUser->promotion_id = $id;
        $promotionUser->status = PromotionUser::STATUS_MODERATION;
        $promotionUser->discount = $promotion->discount;
        $promotionUser->save();

        $this->uploadWorkFiles(
            $promotionUser->id,
            $request->file('work_certificate_file'),
            $request->file('pension_report_file')
        );

        return redirect()->route('studentPromotionList', ['show' => 1]);
    }

    /**
     * @param $promotionUserId
     * @param $workCertificateFile
     * @param $pensionReportFile
     * @return bool
     */
    public function uploadWorkFiles($promotionUserId, $workCertificateFile, $pensionReportFile)
    {
        if(!$workCertificateFile || !$pensionReportFile)
        {
            return false;
        }

        $workCertificateFileName = 'work_certificate_' . $promotionUserId . '.' . pathinfo($workCertificateFile->getClientOriginalName(), PATHINFO_EXTENSION);
        $pensionReportFileName = 'pension_report_' . $promotionUserId . '.' . pathinfo($pensionReportFile->getClientOriginalName(), PATHINFO_EXTENSION);

        $workCertificateFile->move(public_path('images/uploads/works'), $workCertificateFileName);
        $pensionReportFile->move(public_path('images/uploads/works'), $pensionReportFileName);

        $promotionUserWork = new PromotionUserWork();
        $promotionUserWork->promotion_user_id = $promotionUserId;
        $promotionUserWork->work_certificate_file = $workCertificateFileName;
        $promotionUserWork->pension_report_file = $pensionReportFileName;
        return $promotionUserWork->save();
    }
}
