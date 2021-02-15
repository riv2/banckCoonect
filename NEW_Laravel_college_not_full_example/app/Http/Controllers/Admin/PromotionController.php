<?php

namespace App\Http\Controllers\Admin;

use App\PromotionUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PromotionController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getList()
    {
        $promotionUserList = PromotionUser
            ::select([
                'promotion_user.id as id',
                'promotions.name as name',
                'profiles.fio as fio'
            ])
            ->leftJoin('promotions', 'promotions.id', '=', 'promotion_user.promotion_id')
            ->leftJoin('profiles', 'profiles.user_id', '=', 'promotion_user.user_id')
            ->where('promotion_user.status', PromotionUser::STATUS_MODERATION)
            ->get();

        return view('admin.pages.promotion.list', [
            'promotionUserList' => $promotionUserList
        ]);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function info($id)
    {
        $promotion = PromotionUser
            ::select([
                'promotion_user.id as id',
                'promotions.name as name',
                'profiles.fio as fio'
            ])
            ->leftJoin('promotions', 'promotions.id', '=', 'promotion_user.promotion_id')
            ->leftJoin('profiles', 'profiles.user_id', '=', 'promotion_user.user_id')
            ->with('work')
            ->where('promotion_user.status', PromotionUser::STATUS_MODERATION)
            ->where('promotion_user.id', $id)
            ->first();

        if(!$promotion)
        {
            abort(404);
        }

        return view('admin.pages.promotion.info', [
            'promotion' => $promotion
        ]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function infoPost(Request $request, $id)
    {
        $validation = Validator::make([
            'id' => $id,
            'status'=> $request->input('status')
        ],[
            'id' => 'required|exists:promotion_user,id',
            'status' => [Rule::in([
                PromotionUser::STATUS_ACTIVE,
                PromotionUser::STATUS_REJECT,
                PromotionUser::STATUS_MODERATION
            ])]
        ]);

        if($validation->fails())
        {
            return back()->withErrors($validation->errors());
        }

        $promotion = PromotionUser
            ::where('status', PromotionUser::STATUS_MODERATION)
            ->where('id', $id)
            ->first();

        $promotion->status = $request->input('status');
        $promotion->save();

        return redirect()->route('adminPromotionList');
    }
}
