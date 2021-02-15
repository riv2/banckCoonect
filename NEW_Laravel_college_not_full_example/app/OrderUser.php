<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use DB;

/**
 * @property int order_id
 * @property int user_id
 * @property int id
 */
class OrderUser extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'order_user';

    public static function addIfDoesntExist(int $orderId, int $userId, $actionId = null) : ?int
    {
        $exists = self::where('user_id', $userId);

        if($actionId)
        {
            $actionList = [$actionId];

            if(in_array($actionId, [1, 2, 3, 9]))
            {
                $actionList = [1, 2, 3, 9];
            }
            elseif (in_array($orderId, [10, 11]))
            {
                $actionList = [10, 11];
            }

            $exists->leftJoin('orders', 'orders.id', '=', 'order_user.order_id')
                ->whereIn('orders.order_action_id', $actionList);
        }
        else
        {
            $exists->where('order_id', $orderId);
        }

        $exists = $exists->exists();

        if (!$exists) {
            $link = new self;
            $link->order_id = $orderId;
            $link->user_id = $userId;
            $link->save();
            return $link->id;
        }

        return null;
    }

    public static function getUserOrders(int $userId)
    {
        return self::select(
                    'orders.id', 
                    'orders.number', 
                    'order_names.name',
                    'orders.created_at'
                )
                ->leftJoin('orders', 'order_user.order_id', 'orders.id')
                ->leftJoin('order_names', 'order_names.id', 'orders.order_name_id')
                ->where('user_id', $userId)
                ->get();
    }

    public static function getUserOrdersStatusByDate(int $userId, string $date)
    {
        $order = self::select(
            'orders.id',
            'orders.number',
            'orders.date',
            'orders.order_action_id',
            'orders.created_at'
        )
            ->leftJoin('orders', 'order_user.order_id', 'orders.id')
            ->where('user_id', $userId)
            ->where('date', '<=', $date)
            ->orderBy('date', 'desc')
            ->first();

        if (!empty($order) && !is_null($order->order_action_id)) {
            if (in_array($order->order_action_id, [1, 2, 3, 9])) {
                $status =  Profiles::EDUCATION_STATUS_SEND_DOWN;
            }
            elseif (in_array($order->order_action_id, [4, 5])) {
                $status =  Profiles::EDUCATION_STATUS_STUDENT;
            }
            elseif ($order->order_action_id == 6) {
                $status =  Profiles::EDUCATION_STATUS_PREGRADUATE;
            }
            elseif ($order->order_action_id == 7) {
                $status =  Profiles::EDUCATION_STATUS_ACADEMIC_LEAVE;
            }
            elseif ($order->order_action_id == 8) {
                $status =  Profiles::EDUCATION_STATUS_STUDENT;
            }
            elseif (in_array($order->order_action_id, [10, 11])) {
                $status =  Profiles::EDUCATION_STATUS_GRADUATE;
            }
            elseif($order->order_action_id == 12) {
                $status =  Profiles::EDUCATION_STATUS_TEMP_SUSPENDED;
            } else {
                $status =  Profiles::EDUCATION_STATUS_STUDENT;
            }
        } else {
            $status =  Profiles::EDUCATION_STATUS_STUDENT;
        }

        return $status;
    }
}
