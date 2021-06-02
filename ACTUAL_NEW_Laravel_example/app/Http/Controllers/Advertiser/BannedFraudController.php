<?php

namespace App\Http\Controllers\Advertiser;

use App\Exports\FraudsExport;
use App\Filters\BannedFraudFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBannedFraud;
use App\Models\BannedFraud;
use App\Models\Order;
use App\Processors\ProductsOrderProcessor;
use Illuminate\Http\Request;

class BannedFraudController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(BannedFraudFilter $filters)
    {
        $bannedFrauds = BannedFraud::query()
            ->filter($filters)
            ->with('order')
            ->paginate();
        return view('advertiser.banned_frauds.index', [
            'fields' => $filters->fields,
            'bannedFrauds' => $bannedFrauds,
        ]);

    }

    /**
     * @param BannedFraudFilter $filters
     * @return FraudsExport|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export(BannedFraudFilter $filters)
    {
        $file_name = 'banned-frauds_' . md5((string) microtime(true)) . '.xlsx';
        $export = new FraudsExport($filters);
        return $export->download($file_name);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('advertiser.banned_frauds.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreBannedFraud $request)
    {
        $data = $request->validated();
        $data['pp_id'] = auth()->user()->pp_id;

        $bannedFraud = new BannedFraud();
        foreach ($data as $k => $v) {
            $bannedFraud->$k = $v;
        }
        if (!$bannedFraud->save()) {
            return redirect(route("advertiser.banned-frauds.index"))->withErrors('В процессе блокировки произошла ошибка.');
        }
        $processorResult = $this->doProcessingRecalc($bannedFraud->pp_id, $bannedFraud->order_id, $bannedFraud->offer_id , 'ban', $bannedFraud->id);
        if ($processorResult['status'] == 'error') {
            return redirect(route("advertiser.banned-frauds.index"))->withErrors($processorResult['msg']);
        }
        return redirect(route("advertiser.banned-frauds.index"))->with("success", ["Заявка успешно заблокирована"]);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\BannedFraud $bannedFraud
     * @return \Illuminate\Http\Response
     */
    public function show(BannedFraud $bannedFraud)
    {
        $order = $bannedFraud->order()
            ->with('partner')
            ->first();
        return view('advertiser.banned_frauds.show', [
            'bannedFraud' => $bannedFraud,
            'order' => $order,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\BannedFraud $bannedFraud
     * @return \Illuminate\Http\Response
     */
    public function edit(BannedFraud $bannedFraud)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\BannedFraud $bannedFraud
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BannedFraud $bannedFraud)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\BannedFraud $bannedFraud
     * @return \Illuminate\Http\Response
     */
    public function destroy(BannedFraud $bannedFraud)
    {
        $errorMsg = $this->doProcessingRecalc($bannedFraud->pp_id, $bannedFraud->order_id, $bannedFraud->offer_id , 'unban', $bannedFraud->id);
        if (!empty($errorMsg)) {
           return redirect(route('advertiser.banned-frauds.index'))->with('error', ['Не удалось удалить!']);
        }
        $bannedFraud->delete();
        return redirect(route('advertiser.banned-frauds.index'))->with('success', ['Заявка успешно далена из списка заблокированных']);
    }


    protected function doProcessingRecalc($pp_id, $order_id, $offer_id, $type, $bannedFraudId = null)
    {
        $errorMsg = '';

        if ($type == 'unban') {
            $order = Order::query()
                ->where('banned_fraud_id', '=', $bannedFraudId)
                ->first();
        } else {
            $order = Order::query()
                ->where('pp_id', '=', $pp_id)
                ->where('order_id', '=', $order_id)
                ->where('offer_id', '=', $offer_id)
                ->first();
        }

        if ($order) {
            $productsOrderProcessor = new ProductsOrderProcessor($order);
            if ($type == 'ban') {
                $processorResult = $productsOrderProcessor->banOrder('banned_fraud', $bannedFraudId);
            } elseif ($type == 'unban') {
                $processorResult = $productsOrderProcessor->unbanOrder();
            }
            if ($processorResult['status'] == 'error') {
                $errorMsg .= '<br>' . $processorResult['msg'];
            }
        }

        return $errorMsg;
    }
}

