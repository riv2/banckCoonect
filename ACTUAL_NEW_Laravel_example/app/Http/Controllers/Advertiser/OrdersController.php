<?php

namespace App\Http\Controllers\Advertiser;

use App\Exports\OrdersExport;
use App\Filters\OrdersFilter;
use App\Helpers\CalculateFeesHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrdersOrder;
use App\Lists\OrderStateList;
use App\Models\Notify;
use App\Models\Order;
use App\Models\OrdersProduct;
use App\Models\Pp;
use App\Processors\ProductsOrderProcessor;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OrdersController extends Controller
{
    use CalculateFeesHelper;


    /**
     * Display a listing of the resource.
     *
     * @param OrdersFilter $filters
     * @return View
     */
    public function index(OrdersFilter $filters): View
    {
        return view("advertiser.orders.index", [
            "fields" => $filters->fields,
            "orders" => Order::filter($filters)
                ->where("pp_id", "=", auth()->user()->pp->id)
                ->where('type', 'order')
                ->orderBy("datetime", "DESC")
                ->paginate(20)
        ]);
    }

    /**
     * @param OrdersFilter $filters
     * @return OrdersExport|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export(OrdersFilter $filters)
    {
        $file_name = 'orders_' . md5((string) microtime(true)) . '.xlsx';
        $export = new OrdersExport($filters);
        return $export->download($file_name);
    }

    public function recalc(Request $request)
    {
        $errorMsg = $success_msg = '';
        $query    = Order::query()
            ->where("pp_id", "=", Auth()->user()->pp_id);
        $params = 0;
        if ($request->get("date_from")) {
            $query->where("datetime", ">=", $request->get("date_from"));
            $params++;
        }
        if ($request->get("date_to")) {
            $query->where("datetime", "<=", $request->get("date_to"));
            $params++;
        }
        if ($request->get("order_id")) {
            $query->where("order_id", "=", $request->get("order_id"));
            $params++;
        }
        if ($request->get("partner_id")) {
            $query->where("partner_id", "=", $request->get("partner_id"));
            $params++;
        }
        if (!$params) {
            return back()->withErrors([__("Нет параметров для поиска")]);
        }

        /**
         * @var Order $order
         */
        foreach ($query->get() as $order) {
            $productsOrderProcessor = new ProductsOrderProcessor($order);
            $processorResult        = $productsOrderProcessor->process(Auth()->user()->pp->pp_target);
            if ($processorResult['status'] == 'error') {
                $errorMsg .= '<br>' . $processorResult['msg'];
            } else {
                $success_msg = $processorResult['msg'];
            }
        }
        if (!empty($errorMsg)) {
            return back()->withErrors($errorMsg . $success_msg);
        }
        return back()->with('success', $processorResult['msg']);
    }

    public function create()
    {
        return view("advertiser.orders.create");
    }

    public function store(StoreOrdersOrder $request)
    {
        $data = $request->validated();
        $data["pp_id"] = auth()->user()->pp->id;


        $order = new Order();
        foreach ($data as $k => $v) {
            $order->$k = $v;
        }

        $order->save();
        return redirect(route("advertiser.orders.index"))->with("success", ["Заказ создан"]);
    }

    public function show($id)
    {
        $unit = '';
        if (Pp::query()->where('id', '=', auth()->user()->pp->id)->first()->pp_target == 'products') {
            $orderProducts = OrdersProduct::query()->where('order_id', '=', $id)->get();
        } else {
            $orderProducts = [];
        }

        $order = Order::query()
            ->where("order_id", "=", $id)
            ->where('type', 'order')
            ->where("pp_id", "=", auth()->user()->pp->id)
            ->with(['pp', 'offer'])
            ->firstOrFail();

        if ($order && isset($order->offer->fee_type )) {
            if ($order->offer->fee_type == 'fix') {
                $unit = $order->pp->currency;
            } elseif  ($order->offer->fee_type == 'share') {
                $unit = '\%';
            } else {
                $unit = '';
            }
        }

        $notify = Notify::query()
            ->where('partner_id', '=', $order->partner_id)
            ->where('order_id', '=', $id)
            ->orderByDesc('datetime')
            ->limit(10)
            ->get();

        return view("advertiser.orders.show", [
            'order' => $order,
            'orderProducts' => $orderProducts,
            'unit' => $unit,
            'notify' => $notify,
        ]);
    }

    public function edit($id)
    {
        $order = Order::query()
                ->where("order_id", "=", $id)
                ->where("pp_id", "=", auth()->user()->pp->id)
                ->firstOrFail();
        if ($order->reestr_id) {
            return back()->withErrors(__('advertiser.orders.edit.without-edit'));
        }
        return view("advertiser.orders.edit", [
            "order" => $order]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Order $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Order $order)
    {
        $tmp = [];
        foreach (User::getPartners(auth()->user()) as $item) {
            array_push($tmp, $item->id);
        }

        $fields = [
            'status' => ['required', Rule::in(array_keys(OrderStateList::getList()))],
            'datetime' => 'required|date',
            'partner_id' => ['integer', Rule::in($tmp)],
            'link_id' => 'integer',
            'click_id' => 'string|nullable',
            'web_id' => 'string|nullable',
            'client_id' => 'string|nullable',
            'wholesale' => 'boolean',
        ];
        $request->validate($fields);

        $order->status = $request->get('status');
        $order->datetime = $request->get('datetime');
        $order->model = $request->get('model');
        $order->partner_id = $request->get('partner_id');
        $order->link_id = $request->get('link_id');
        $order->click_id = $request->get('click_id');
        $order->web_id = $request->get('web_id');
        $order->client_id = $request->get('client_id');
        $order->wholesale = $request->get('wholesale');
        $order->save();

        return redirect(route('advertiser.orders.edit', ['order' => $order]))->with('success', ['Заказ сохранен']);
    }

    /**
     * @param Request $request
     * @return View|\Illuminate\Contracts\View\Factory
     */
    public function importForm(Request $request)
    {
        return view('advertiser.orders.import', [
            //
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'order.*.order_id' => 'required',
            'order.*.offer_id' => 'required|numeric',
            'order.*.partner_id' => 'required|numeric',
            'order.*.link_id' => 'required|numeric',
            'order.*.gross_amount' => 'required|numeric',
            'order.*.datetime' => 'required|string',
        ]);
        dump($request->get('order'));
        return "$request->get('order')";
    }
}
