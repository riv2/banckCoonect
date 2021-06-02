<?php

namespace App\Http\Controllers\Advertiser;

use App\Filters\BannedLinksFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBannedLink;
use App\Models\BannedLink;
use App\Models\Order;
use App\Processors\ProductsOrderProcessor;
use Illuminate\Http\Request;

class BannedLinkController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(BannedLinksFilter $filters)
    {
        $bannedLinks = BannedLink::query()
            ->filter($filters)
            ->with('link')
            ->orderBy('created_at', 'DESC')
            ->paginate();
        return view('advertiser.banned_link.index', [
            'fields' => $filters->fields,
            'bannedLinks' => $bannedLinks,
        ]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('advertiser.banned_link.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreBannedLink $request)
    {
        $data = $request->validated();
        $data['pp_id'] = auth()->user()->pp_id;

        $bannedLink = new BannedLink();
        foreach ($data as $k => $v) {
            $bannedLink->$k = $v;
        }
        if (!$bannedLink->save()) {
            return redirect(route("advertiser.banned-links.index"))->withErrors('Неизвестная ошибка.');
        }
        $errorMsg = $this->doProcessingRecalc($bannedLink->pp_id, $bannedLink->link_id , 'ban', $bannedLink->id, $bannedLink->web_id);
        if (!empty($errorMsg)) {
            return redirect(route("advertiser.banned-links.index"))->withErrors($errorMsg);
        }
        return redirect(route("advertiser.banned-links.index"))->with("success", ["Ссылка успешно заблокирована"]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BannedLink  $bannedLink
     * @return \Illuminate\Http\Response
     */
    public function show(BannedLink $bannedLink)
    {
        $link = $bannedLink->link()
            ->with('partner')
            ->first();
        return view('advertiser.banned_link.show', [
            'bannedLink' => $bannedLink,
            'link' => $link,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BannedLink  $bannedLink
     * @return \Illuminate\Http\Response
     */
    public function edit(BannedLink $bannedLink)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BannedLink  $bannedLink
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BannedLink $bannedLink)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BannedLink  $bannedLink
     * @return \Illuminate\Http\Response
     */
    public function destroy(BannedLink $bannedLink)
    {
        $errorMsg = $this->doProcessingRecalc($bannedLink->pp_id, $bannedLink->link_id , 'unban', $bannedLink->id, $bannedLink->web_id);
        if (!empty($errorMsg)) {
            return redirect(route("advertiser.banned-links.index"))->withErrors($errorMsg);
        }
        $bannedLink->delete();
        return redirect(route('advertiser.banned-links.index'));
    }


    protected function doProcessingRecalc($pp_id = null, $link_id = null, $type = null, $bannedLinkId = null, $web_id = null) {
        $errorMsg = '';

        if ($type == 'unban') {
            $orders = Order::query()
                ->where('banned_link_id', '=', $bannedLinkId)
                ->get();
        } else {
            if (!is_null($web_id)) {
                $orders = Order::query()
                    ->where('pp_id', '=', $pp_id)
                    ->where('web_id', '=', $web_id)
                    ->where('link_id', '=', $link_id)
                    ->get();
            } else {
                $orders = Order::query()
                    ->where('pp_id', '=', $pp_id)
                    ->where('link_id', '=', $link_id)
                    ->get();
            }
        }

        if ($orders) {
            foreach ($orders as $order) {
                $productsOrderProcessor = new ProductsOrderProcessor($order);
                if ($type == 'ban') {
                    $processorResult = $productsOrderProcessor->banOrder('banned_links', $bannedLinkId);
                } elseif ($type == 'unban') {
                    $processorResult = $productsOrderProcessor->unbanOrder();
                }
                if ($processorResult['status'] == 'error') {
                    $errorMsg .= '<br>' . $processorResult['msg'];
                }
            }
        }
        return $errorMsg;
    }


}
