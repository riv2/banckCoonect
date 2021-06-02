<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Lists\OrderStatusList;
use App\Models\ApiLog;
use App\Models\Order;
use App\Models\Pp;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;

class ApiOrdersController extends Controller
{
    public function orders(Request $request)
    {
        App::setLocale('en');

        $validator = Validator::make($request->all(), [
            'dateStart' => 'date',
            'dateEnd' => 'date',
            'status' => 'string',
            'hash' => 'required|string',
        ]);

        $apiLog = new ApiLog();
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

        $correctStatuses = OrderStatusList::getList();
        if (!is_null($request->get('status'))) {
            $requestStatuses = explode(',', $request->get('status'));
            foreach ($requestStatuses as $status) {
                if (!in_array($status, $correctStatuses)) {
                    $massage = 'Status "' . $status . '" is not allowed, please use the correct status value';
                    $apiLog->data_out = $massage;
                    $apiLog->result = 400;
                    $apiLog->save();

                    return response($massage, 400);
                }
            }
        }

        $pp = Pp::query()
            ->where('tech_domain', '=', $request->getHost())
            ->orWhere('prod_domain', '=', $request->getHost())
            ->first();
        $users = User::query()
            ->where('pp_id', '=', $pp->id)
            ->where('role', '=', 'partner')
            ->get();
        $isCorrectUser = false;

        foreach ($users as $user) {
            if ($user->hash_name == $request->get('hash')) {
                $correctUser = $user;
                $isCorrectUser = true;
                break;
            }
        }

        if (!$isCorrectUser) {
            $massage = 'Access denied, please use the correct hash name';
            $apiLog->data_out = $massage;
            $apiLog->result = 403;
            $apiLog->save();

            return response($massage, 403);
        }
        $dateStart = $request->get('dateStart') ?? Carbon::now()->subMonth()->toDateString();
        $dateEnd = $request->get('dateEnd') ?
            Carbon::parse($request->get('dateEnd'))->endOfDay()->toDateTimeString() :
            Carbon::now()->endOfDay()->toDateTimeString();
        $data_out = collect();

        if (!is_null($request->get('status'))) {
            $count = count($requestStatuses);
            if ($count == 1) {
                $orders = Order::query()
                    ->where('pp_id', '=', $pp->id)
                    ->where('partner_id', '=', $correctUser->id)
                    ->whereBetween('datetime', [$dateStart, $dateEnd])
                    ->where('status', '=', $requestStatuses[0])
                    ->withoutGlobalScopes()
                    ->get();
            } elseif ($count == 2) {
                $orders = Order::query()
                    ->where('pp_id', '=', $pp->id)
                    ->where('partner_id', '=', $correctUser->id)
                    ->whereBetween('datetime', [$dateStart, $dateEnd])
                    ->where('status', '=', $requestStatuses[0])
                    ->orWhere('status', '=', $requestStatuses[1])
                    ->withoutGlobalScopes()
                    ->get();
            } else {
                $orders = Order::query()
                    ->where('pp_id', '=', $pp->id)
                    ->where('partner_id', '=', $correctUser->id)
                    ->whereBetween('datetime', [$dateStart, $dateEnd])
                    ->withoutGlobalScopes()
                    ->get();
            }
        } else {
            $orders = Order::query()
                ->where('pp_id', '=', $pp->id)
                ->where('partner_id', '=', $correctUser->id)
                ->whereBetween('datetime', [$dateStart, $dateEnd])
                ->withoutGlobalScopes()
                ->get();
        }

        foreach ($orders as $order) {
            $orderObject = [
                'id' => $order->id,
                'datetime' => $order->datetime,
                'link_id' => $order->link_id,
                'link_name' => $order->link->link_name,
                'status' => $order->status,
                'total' => $order->gross_amount,
                'click_id' => $order->click_id,
                'web_id' => $order->web_id,
                'amount' => $order->amount,
            ];
            if ($pp->pp_target == 'products') {
                foreach ($order->products as $product) {
                    $orderObject['products'][] = [
                        'product_id' => $product->product_id,
                        'product_name' => $product->product_name,
                        'category_name' => $product->category,
                        'business_unit' => $product->fee_id,
                        'fee' => $product->fee,
                        'price' => $product->price,
                        'quantity' => $product->quantity,
                        'total' => $product->total,
                        'status' => $product->status,
                        'amount' => $product->amount,
                    ];
                }
            }
            $data_out->push($orderObject);
        }

        if (!empty($data_out->all())) {
            $apiLog->data_out = $data_out;
            $apiLog->result = 200;
        } else {
            $data_out = 'Sorry, no orders found - change request or get back later';
            $apiLog->data_out = $data_out;
            $apiLog->result = 404;
        }
        $apiLog->save();
        $response = $data_out->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        return response($response);
    }

    public function orders_paid(Request $request)
    {
        App::setLocale('en');

        $validator = Validator::make($request->all(), [
            'payed_month' => 'required|date',
            'hash' => 'required|string',
        ]);

        $apiLog = new ApiLog();
        $apiLog->datetime = Carbon::now()->toDateTimeString();
        $apiLog->data_in = $request->fullUrl();
        $apiLog->save();

        if ($validator->fails()) {
            $apiLog->data_out = $validator->errors();
            $apiLog->result = 400;
            $apiLog->save();

            return response($validator->errors(), 400);
        }

        $pp = Pp::query()
            ->where('tech_domain', '=', $request->getHost())
            ->orWhere('prod_domain', '=', $request->getHost())
            ->first();
        $users = User::query()
            ->where('pp_id', '=', $pp->id)
            ->where('role', '=', 'partner')
            ->get();
        $isCorrectUser = false;

        foreach ($users as $user) {
            if ($user->hash_name == $request->get('hash')) {
                $correctUser = $user;
                $isCorrectUser = true;
                break;
            }
        }

        if (!$isCorrectUser) {
            $massage = 'Access denied, please use the correct hash name';
            $apiLog->data_out = $massage;
            $apiLog->result = 403;
            $apiLog->save();

            return response($massage, 403);
        }

        $dateStart = Carbon::parse($request->get('payed_month'))->startOfMonth()->toDateTimeString();
        $dateEnd = Carbon::parse($request->get('payed_month'))->endOfMonth()->toDateTimeString();
        $data_out = collect();

        $orders = Order::query()
            ->where('pp_id', '=', $pp->id)
            ->where('partner_id', '=', $correctUser->id)
            ->where('status', '=', 'sale')
            ->whereBetween('datetime', [$dateStart, $dateEnd])
            ->withoutGlobalScopes()
            ->get();

        foreach ($orders as $order) {
            $orderObject = [
                'id' => $order->id,
                'datetime' => $order->datetime,
                'link_id' => $order->link_id,
                'link_name' => $order->link->link_name,
                'status' => $order->status,
                'total' => $order->gross_amount,
                'click_id' => $order->click_id,
                'web_id' => $order->web_id,
                'amount' => $order->amount,
            ];
            if ($pp->pp_target == 'products') {
                foreach ($order->products as $product) {
                    if ($product->status == 'sale') {
                        $orderObject['products'][] = [
                            'product_id' => $product->product_id,
                            'product_name' => $product->product_name,
                            'category_name' => $product->category,
                            'business_unit' => $product->fee_id,
                            'fee' => $product->fee,
                            'price' => $product->price,
                            'quantity' => $product->quantity,
                            'total' => $product->total,
                            'status' => $product->status,
                            'amount' => $product->amount,
                        ];
                    }
                }
            }
            $data_out->push($orderObject);
        }

        if (!empty($data_out->all())) {
            $apiLog->data_out = $data_out;
            $apiLog->result = 200;
        } else {
            $data_out = 'Sorry, no orders found - change request or get back later';
            $apiLog->data_out = $data_out;
            $apiLog->result = 404;
        }
        $apiLog->save();
        $response = $data_out->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        return response($response);
    }
}
