<?php

namespace App\Http\Controllers\Advertiser;

use App\Helpers\DataBaseHelper;
use App\Http\Controllers\Controller;
use App\Lists\OrderStatusList;
use App\Models\ApiLog;
use App\Models\Link;
use App\Models\Offer;
use App\Models\Order;
use App\Models\OrdersProduct;
use App\Models\Pp;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function index(Request $request, int $pp_id)
    {
        App::setLocale('en');

        $validator = Validator::make($request->all(), [
            'order_id' => 'required|string',
            'url' => 'required|string',
            'offer_id' => 'required|string',
            'gross_amount' => 'required|numeric',
            'status' => ['required', 'string', Rule::in(OrderStatusList::getList())],
            'hash' => 'required|string',
        ]);

        $apiLog = new ApiLog();
        $apiLog->offer_id = $request->get('offer_id');
        $apiLog->order_id = $request->get('order_id');
        $apiLog->datetime = Carbon::now()->toDateTimeString();
        $apiLog->data_in = $request->fullUrl();
        $apiLog->status = $request->get('status');
        $apiLog->save();

        if ($validator->fails()) {
            $apiLog->data_out = $validator->errors();
            $apiLog->result = 400;
            $apiLog->save();

            return response($validator->errors(), 400);
        }

        $pp = Pp::query()->find($pp_id);

        if (is_null($pp)) {
            $massage = 'Partner program with id=' . $pp_id . 'does not exist';
            $apiLog->data_out = $massage;
            $apiLog->result = 400;
            $apiLog->save();

            return response($massage, 400);
        }
        $user = User::query()->find($pp->user_id);

        if ($user->hash_name != $request->get('hash')) {
            $massage = 'Access denied, please use the correct hash name';
            $apiLog->data_out = $massage;
            $apiLog->result = 403;
            $apiLog->save();

            return response($massage, 403);
        }

        $offer = Offer::query()
            ->where('user_id', '=', $user->id)
            ->where('id', '=', $request->get('offer_id'))
            ->first();
        if (is_null($offer)) {
            $massage = 'No such offer found, please use the correct offer_id';
            $apiLog->data_out = $massage;
            $apiLog->result = 404;
            $apiLog->save();

            return response($massage, 404);
        }
        parse_str(parse_url(urldecode($request->get('url')))['query'], $urlParameters);
        $link = Link::query()
            ->where('pp_id', '=', $pp_id)
            ->where('partner_id', '=', $urlParameters['utm_content'])
            ->where('id', '=', $urlParameters['utm_campaign'])
            ->where('offer_id', '=', $offer->id)
            ->first();

        if (is_null($link)) {
            $massage = 'No such link found, please use the correct link';
            $apiLog->data_out = $massage;
            $apiLog->result = 404;
            $apiLog->save();

            return response($massage, 404);
        }


        $order = Order::query()
            ->where('order_id', '=', $request->get('order_id'))
            ->where('pp_id', '=', $pp_id)
            ->first();

        if (is_null($order)) {
            $isNewOrder = true;
            $order = new Order();
            $order->order_id = $request->get('order_id');
            $order->click_id = $request->get('click_id') ?? null;
            $order->web_id = $urlParameters['utm_term'] ?? null;
            $order->partner_id = $urlParameters['utm_content'];
            $order->link_id = $urlParameters['utm_campaign'];
            $order->offer_id = $request->get('offer_id');
            $order->status = 'new';
            $order->pp_id = $pp_id;
            $order->datetime = Carbon::now()->toDateTimeString();
            $order->gross_amount = $request->get('gross_amount');
            $order->cnt_products = 1;
            $order->save();

            if ($pp->pp_target == 'products') {
                $product = new OrdersProduct();
                $product->order_id = $request->get('order_id');
                $product->parent_id = Order::query()
                    ->where('order_id', '=', $request->get('order_id'))
                    ->where('pp_id', '=', $pp_id)
                    ->first()->id;
                $product->pp_id = $pp_id;
                $product->partner_id = $urlParameters['utm_content'];
                $product->offer_id = $request->get('offer_id');
                $product->link_id = $urlParameters['utm_campaign'];
                $product->product_id = $request->get('order_id');
                $product->product_name = 'product';
                $product->price = $request->get('gross_amount');
                $product->quantity = 1;
                $product->total = $product->price * $product->quantity;
                $product->status = 'new';
                $product->click_id = $request->get('click_id') ?? null;
                $product->web_id = $urlParameters['utm_term'] ?? null;
                $product->datetime = Carbon::now()->toDateTimeString();
                $product->amount = 0;
                $product->amount_advert = 0;
                $product->fee_advert = 0;
                $product->save();
            }
        } else {
            $isNewOrder = false;
            $order->status = $request->get('status');
            $order->pp_id = $pp_id;
            $order->datetime = Carbon::now()->toDateTimeString();
            $order->gross_amount = $request->get('gross_amount');
            $order->save();
        }

        if ($isNewOrder) {
            if (Order::query()->find($request->get('order_id'))->gross_amount == $request->get('gross_amount')) {
                $massage = 'Success, order created. Thank you!';
                $apiLog->data_out = $massage;
                $apiLog->result = 200;
                $apiLog->save();

                return response($massage);
            } else {
                $massage = 'Sorry, some server errors occured';
                $apiLog->data_out = $massage;
                $apiLog->result = 500;
                $apiLog->save();

                return response($massage, 500);
            }
        }

        if (Order::query()->find($request->get('order_id'))->status == $request->get('status') && Order::query()->find($request->get('order_id'))->gross_amount == $request->get('gross_amount')) {
            $massage = 'Success, order\'s data updated. Thank you!';
            $apiLog->data_out = $massage;
            $apiLog->result = 200;
            $apiLog->save();

            return response($massage);
        } else {
            $massage = 'Sorry, some server errors occured';
            $apiLog->data_out = $massage;
            $apiLog->result = 500;
            $apiLog->save();

            return response($massage, 500);
        }
    }
}
