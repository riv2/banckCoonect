<?php


namespace App\Services;


use App\OrderUser;
use App\UserApplication;

class UserApplicationService
{
    /**
     * @param $userApplicationId
     * @param $orderId
     * @return bool
     */
    static function confirm($userApplicationId, $orderId)
    {
        $userApplication = UserApplication::where('id', $userApplicationId)->first();

        if($userApplication)
        {
            $addRelation = OrderUser::addIfDoesntExist($orderId, $userApplication->user_id);

            if($addRelation)
            {
                $userApplication->status = UserApplication::STATUS_CONFIRM;
                $userApplication->order_id = $orderId;
                $userApplication->save();

                return true;
            }
        }

        return false;
    }

    /**
     * @param $userApplicationId
     * @param $comment
     * @return bool
     */
    static function decline($userApplicationId, $comment)
    {
        $userApplication = UserApplication::where('id', $userApplicationId)->first();

        if($userApplication)
        {
            $userApplication->comment = ($userApplication->comment ? $userApplication->comment . "\n\n" : '') . $comment;
            $userApplication->status = UserApplication::STATUS_DECLINE;
            $userApplication->save();

            return true;
        }

        return false;
    }
}