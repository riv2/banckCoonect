<?php

namespace App\Http\Controllers\Balance;

use App\Models\BalanceHistory;
use AvtoDev\JsonRpc\Requests\RequestInterface;


class BalanceController
{
    /**
     * Get balance of user
     *
     * @param RequestInterface $request
     *
     * @return int
     */
    public function userBalance(RequestInterface $request)
    {
        $balance        = 0;
        $user_id        = 0;
        $params         = $request->getParams();

        if (isset($params->user_id)) {
            $user_id = (int)$params->user_id;
        }

        $userBalance = BalanceHistory::where('user_id', $user_id)
                        ->latest('created_at', 'desc')
                        ->first();

        if (isset($userBalance['balance'])) {
            $balance = $userBalance['balance'];
        }

        return $balance;
    }

    /**
     * Get balance history for user.
     *
     * @param RequestInterface $request
     *
     * @return mixed
     */
    public function history(RequestInterface $request)
    {
        $limit          = 50;
        $params         = $request->getParams();

        if (isset($params->limit)) {
            $limit = (int)$params->limit;
        }

        $balanceHistory = BalanceHistory::whereNotNull('id')->limit($limit)->get()->toArray();

        return $balanceHistory;
    }
}
