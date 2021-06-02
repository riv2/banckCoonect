<?php

namespace App\Http\Controllers\Analyst;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrdersProduct;
use App\Http\Requests\UpdateOrdersProduct;
use App\Models\Order;
use App\Models\OrdersProduct;
use Illuminate\Http\RedirectResponse;

class OrdersProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param $order_id
     * @return \Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create($order_id)
    {
        return view("analyst.products.create", [
            'order' => Order::query()
                ->where("order_id", "=", $order_id)
                ->where("pp_id", "=", auth()->user()->pp->id)
                ->firstOrFail()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Order $order
     * @param StoreOrdersProduct $request
     * @return RedirectResponse|
     */
    public function store(Order $order, StoreOrdersProduct $request): RedirectResponse
    {
        $data = $request->validated();

        $data['order_id'] = $order->order_id;
        $data['parent_id'] = $order->id;
        $data['datetime'] = $order->datetime;
        $data['partner_id'] = $order->partner_id;
        $data['link_id'] = $order->link_id;
        $data["pp_id"] = auth()->user()->pp->id;

        $product = new OrdersProduct();
        foreach ($data as $k => $v) {
            $product->$k = $v;
        }
        $product->save();
        return redirect(route("analyst.orders.show", $order->order_id))->with("success", ["Продукт создан"]);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\OrdersProduct $ordersProduct
     * @return \Illuminate\Http\Response
     */
    public function show(OrdersProduct $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $order_id
     * @param int $product_id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function edit($order_id, int $product_id)
    {
        $product = OrdersProduct::query()
            ->with('order')
            ->where("order_id", "=", $order_id)
            ->where("product_id", "=", $product_id)
            ->where("pp_id", "=", auth()->user()->pp->id)
            ->firstOrFail();

        return view("analyst.products.edit", [
            'product' => $product
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateOrdersProduct $request
     * @param \App\Models\OrdersProduct $ordersProduct
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function update(UpdateOrdersProduct $request, OrdersProduct $ordersProduct)
    {
        $ordersProduct = OrdersProduct::query()
            ->where("order_id", "=", $request->get('order_id'))
            ->where("product_id", "=", $request->get('product_id'))
            ->where("pp_id", "=", auth()->user()->pp->id)
            ->firstOrFail();

        $request->validated();

        $ordersProduct->product_id = $request->get('product_id');
        $ordersProduct->product_name = $request->get('product_name');
        $ordersProduct->quantity = $request->get('quantity');
        $ordersProduct->price = $request->get('price');
        $ordersProduct->amount = $request->get('amount');
        $ordersProduct->save();

        return redirect(route("analyst.orders.show", $ordersProduct->order_id))->with("success", ["Продукт обновлен"]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\OrdersProduct $ordersProduct
     * @return \Illuminate\Http\Response
     */
    public function destroy(OrdersProduct $ordersProduct)
    {
        //
    }
}
