<?php
/**
 * Project qpartners
 * Created by danila 01.06.20 @ 20:42
 */

namespace App\Http\Controllers\Partner;


use App\Http\Controllers\Controller;
use App\Lists\PaymentStatusesList;
use App\Models\PartnerPayment;
use App\Models\Reestr;

class PaymentsController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $user->load('pay_methods');
        $totals = PartnerPayment::query()->where("partner_id", "=", Auth()->id())->
        selectRaw('SUM(revenue) as revenue, status')
            ->groupBy('status')
            ->get()->pluck('revenue', 'status')->toArray();
        foreach (PaymentStatusesList::getList() as $k => $v) {
            if (!isset($totals[$k])) {
                $totals[$k] = 0;
            }
        }
        $totals['all'] = array_sum(array_values($totals));

        $payments = PartnerPayment::query()
            ->where('partner_id', '=', Auth()->id())
            ->with(['payMethod', 'payAccount'])
            ->get();
        $max = 0;
        foreach ($payments as $p) {
            if ($p->revenue > $max) {
                $max = $p->revenue;
            }
            $p['datetime'] = Reestr::query()->find($p->reestr_id)->datetime;
        }
        $totals['max'] = $max;

        return view('partner.payments', [
            'totals' => $totals,
            'payments' => $payments,
            'statuses' => PaymentStatusesList::getList()
        ]);
    }
}
