<?php

namespace App\Http\Controllers\Partner;

use App\Exports\OrdersExport;
use App\Filters\OrdersFilter;
use App\Http\Controllers\Controller;
use App\Models\Notify;
use App\Models\Order;
use App\Models\OrdersProduct;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param OrdersFilter $filter
     * @return View
     */
    public function index(OrdersFilter $filter): View
    {
        return view('partner.orders.index', [
            'fields' => $filter->fields,
            'orders' => Order::query()
                ->filter($filter)
                ->where('pp_id', '=', auth()->user()->pp_id)
                ->orderBy('datetime', 'DESC')
                ->paginate(20),
        ]);
    }

    public function show($id)
    {
        $order = Order::query()
            ->where('order_id', '=', $id)
            ->where('pp_id', '=', auth()->user()->pp_id)
            ->with(['pp', 'offer'])
            ->firstOrFail();

        if ($order->offer->fee_type == 'fix') {
            $unit = $order->pp->currency;
        } elseif ($order->offer->fee_type == 'share') {
            $unit = '%';
        } else {
            $unit = '';
        }

        $orderProducts = [];
        if (auth()->user()->pp->pp_target == 'products') {
            $orderProducts = OrdersProduct::query()->where('order_id', '=', $id)->get();
        }

        $notify = Notify::query()
            ->where('partner_id', '=', auth()->id())
            ->where('order_id', '=', $id)
            ->orderByDesc('datetime')
            ->limit(10)
            ->get();

        return view('partner.orders.show', [
            'order' => $order,
            'orderProducts' => $orderProducts,
            'notify' => $notify,
            'unit' => $unit,
        ]);
    }

    /**
     * @param OrdersFilter $filter
     * @return OrdersExport|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export(OrdersFilter $filter)
    {
        $file_name = 'orders_' . md5((string) microtime(true)) . '.xlsx';
        $export = new OrdersExport($filter);
        return $export->download($file_name);
    }
}
