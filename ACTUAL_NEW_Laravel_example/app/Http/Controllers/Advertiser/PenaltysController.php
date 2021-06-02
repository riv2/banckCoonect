<?php

namespace App\Http\Controllers\Advertiser;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrdersPenalty;
use App\Http\Requests\UpdateOrdersPenalty;
use App\Models\Order;
use Illuminate\Http\Request;

class PenaltysController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        return view("advertiser.penaltys.index", [
            "orders" => Order::query()
                ->where("pp_id", "=", auth()->user()->pp->id)
                ->where('type', '!=', 'order')
                ->orderBy("datetime", "DESC")
                ->paginate(20)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('advertiser.penaltys.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function store(StoreOrdersPenalty $request)
    {
        $data = $request->validated();
        $data["pp_id"] = auth()->user()->pp->id;

        $penalty = new Order();
        foreach ($data as $k => $v) {
            $penalty->$k = $v;
        }

        $penalty->save();
        return redirect(route('advertiser.penaltys.index'))->with("success", [__('advertiser.penaltys.create.success')]);
    }

    /**
     * Display the specified resource.
     *
     * @param Order $penalty
     * @return \Illuminate\Http\Response
     */
    public function show(Order $penalty)
    {
        return view("advertiser.penaltys.show", [
            'penalty' => $penalty
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Order $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function edit($order_id)
    {
        $penalty = Order::query()
                ->where("order_id", "=", $order_id)
                ->where("pp_id", "=", auth()->user()->pp->id)
                ->firstOrFail();
        if ($penalty->reestr_id) {
            return back()->withErrors(__('advertiser.penaltys.edit.without-edit'));
        }
        return view('advertiser.penaltys.edit', [
            'penalty' => $penalty]);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Order $order
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateOrdersPenalty $request, Order $order)
    {
        $order = Order::query()
            ->where("order_id", "=", $request->get('order_id'))
            ->where("pp_id", "=", auth()->user()->pp->id)
            ->firstOrFail();

        $request->validated();

        $order->type = $request->get('type');
        $order->offer_id = $request->get('offer_id');
        $order->partner_id = $request->get('partner_id');
        $order->gross_amount = $request->get('gross_amount');
        $order->datetime = $request->get('datetime');
        $order->comment = $request->get('comment');
        $order->save();

        return redirect(route('advertiser.penaltys.index'))->with("success", [__('advertiser.penaltys.edit.success')]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Order $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        //
    }
}
