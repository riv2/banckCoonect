<?php

namespace App\Http\Controllers\Admin;

use App\Order;
use App\OrderAction;
use App\OrderName;
use App\OrderUser;
use App\OrderUserSignature;
use App\Profiles;
use App\Services\{Auth, DocxHelper, PhpOfficeHelper};
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\{File,Response};

class OrderController extends Controller
{

    /**orderEdit
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getList(Request $request)
    {
        $orderList = Order::get();

        return view('admin.pages.orders.list', compact('orderList'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request, $id)
    {
        $orderNames = OrderName::get();
        $orderActions = OrderAction::get();

        $order = null;

        if ($id != 'new' && is_numeric($id)) {
            $order = Order::where('id', $id)->first();
        } else {
            $order = new Order();
        }

        if (!$order) {
            abort(404);
        }

        return view('admin/pages/orders/edit', compact('order', 'orderNames', 'orderActions'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function editPost(Request $request, $id)
    {
        $order = null;

        if ($id != 'new' && is_numeric($id)) {
            $order = Order::where('id', $id)->first();
        } else {
            $order = new Order();
        }

        if (!$order) {
            abort(404);
        }

        $order->fill($request->all());
        $order->save();

        return redirect()->route('adminOrderEdit', [
            'id' => $order->id
        ])->with('flash_message', 'Изменения сохранены');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function attachUsers(Request $request)
    {
        $userIds = $request->input('users');

        /** @var Order $order */
        $order = Order::where('id', $request->input('order_id'))->first();

        if (empty($userIds) || empty($order)) {
            abort(404);
        }

        foreach ($userIds as $userId) {
            $order->attachUser($userId);
        }

        return Response::json();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function detachUsers(Request $request)
    {
        $userList = $request->input('users');
        $order = Order::where('id', $request->input('order_id'))->first();

        if(!$userList || !$order)
        {
            abort(404);
        }

        foreach ($userList as $userId)
        {
            $relation = OrderUser::where('user_id', $userId)->where('order_id', $order->id)->first();
            if($relation)
            {
                $relation->delete();

                if(in_array($order->order_action_id, [1, 2, 3, 9, 6, 7, 10, 11, 12])) {
                    Profiles::changeEducationStatus($userId, Profiles::EDUCATION_STATUS_STUDENT);
                } 
                elseif ($order->order_action_id == 4) {
                    Profiles::changeEducationStatus($userId, Profiles::EDUCATION_STATUS_MATRICULANT);
                } 
                elseif ($order->order_action_id == 8) {
                    Profiles::changeEducationStatus($userId, Profiles::EDUCATION_STATUS_ACADEMIC_LEAVE);
                }
            }
        }

        return Response::json();
    }

    public function printOrder(Request $request, $id)
    {
        $order = Order::where('id', $id)->first();

        if(!$order)
        {
            abort(404);
        }

        $OUsers = $order->users;
        $params = [
            't_number'      => $order->number,
            't_date'        => date('d.m.Y', strtotime($order->date)),
            't_name'        => $order->orderName->name,
            't_npa'         => $order->npa,
            't_action'      => $order->action->name,
        ];

        $file1 = DocxHelper::replace(resource_path('docx/order_template.docx'), $params, 'docx');
        $file = PhpOfficeHelper::addTableForOrder($file1,$OUsers);
        File::delete($file1);

        return Response::download($file, 'Приказ.docx')->deleteFileAfterSend(true);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id)
    {
        $order = Order::where('id', $id)->first();

        if($order)
        {
            $order->delete();
        }

        return redirect()->route('adminOrderList');
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function addSignature($id)
    {
        $order = Order::where('id', $id)->first();

        if($order)
        {
            $orderUserSignature = OrderUserSignature
                ::where('user_id', Auth::user()->id)
                ->where('order_id', $order->id)
                ->first();

            if(!$orderUserSignature)
            {
                $orderUserSignature = new OrderUserSignature();
                $orderUserSignature->user_id = Auth::user()->id;
                $orderUserSignature->order_id = $order->id;
            }

            $orderUserSignature->signed = true;
            $orderUserSignature->save();

            return Response::json([
                'status' => true
            ]);
        }

        return Response::json([
            'status' => false
        ]);
    }
}
