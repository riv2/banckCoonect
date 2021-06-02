<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\Notify;
use App\Models\NotifyParam;
use Illuminate\Http\Request;

class PostbacksController extends Controller
{
    public function index()
    {
        $pb = NotifyParam::query()->where('partner_id', '=', auth()->id())->first();
        if (!$pb) {
            $pb = new NotifyParam();
        }
        $notify = Notify::query()
            ->where('partner_id', '=', auth()->id())
            ->orderByDesc('datetime')
            ->limit(10)
            ->get();

        return response()
            ->view('partner.integration.postbacks', [
                'notify_params' => $pb,
                'notify' => $notify,
            ]);
    }

    public function store(Request $request)
    {
        /** @var NotifyParam */
        $pb = NotifyParam::query()
            ->where('partner_id', '=', auth()->id())
            ->first();

        if (!$request->get('postback_url') && $pb) {
            // Пустой URL и есть настройка - удаляем настройку
            $pb->delete();
            return redirect()->route('partner.postbacks')->withSuccess(__('partners.postbacks.settings.deleted_success'));
        } elseif ($request->get('postback_url')) {
            $request->validate([
                'postback_url' => 'url',
                'postback_auth' => 'string|nullable',
                'method' =>  'string|nullable',
                'order_id' => 'string',
                'status' => 'string',
                'amount' => 'string|nullable',
                'gross_amount' => 'string|nullable',
                'status_new_value' => 'string|nullable',
                'status_approve_value' => 'string|nullable',
                'status_sale_value' => 'string|nullable',
                'status_reject_value' => 'string|nullable',
                'web_id' => 'string|nullable',
                'click_id' => 'string|nullable',
                'fee_id' => 'string|nullable',
            ]);

            if (!$pb) {
                $pb = new NotifyParam();
                $pb->partner_id = auth()->id();
            }

            $pb->postback_url = $request->get('postback_url');
            $pb->postback_auth = $request->get('postback_auth');
            $pb->method = $request->get('method');
            $pb->order_id = $request->get('order_id');
            $pb->status = $request->get('status');
            $pb->amount = $request->get('amount');
            $pb->gross_amount = $request->get('gross_amount');
            $pb->status_new_value = $request->get('status_new_value');
            $pb->status_approve_value = $request->get('status_approve_value');
            $pb->status_sale_value = $request->get('status_sale_value');
            $pb->status_reject_value = $request->get('status_reject_value');
            $pb->web_id = $request->get('web_id');
            $pb->click_id = $request->get('click_id');
            $pb->fee_id = $request->get('fee_id');
            $pb->save();
        }

        return redirect()->route('partner.postbacks')->withSuccess(__('partners.postbacks.settings.updated_success'));
    }
}
