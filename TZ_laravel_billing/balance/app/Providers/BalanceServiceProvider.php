<?php

namespace App\Providers;

use App\Models\BalanceHistory;
use Illuminate\Support\ServiceProvider;

class BalanceServiceProvider extends ServiceProvider
{
    /**
     * Get balance of user
     * @param $user_id int
     * @return int
     */
    public function userBalance($user_id)
    {
        $balance = 0;

        $userBalance = BalanceHistory::where('user_id', (int)$user_id)
            ->latest('created_at', 'desc')
            ->first();

        if (isset($userBalance['balance'])) {
            $balance = $userBalance['balance'];
        }

        return $balance;

    }

    /**
     * Get balance history for user.
     * @param $limit int
     * @return mixed
     */
    public function history($limit)
    {
        $balanceHistory = BalanceHistory::whereNotNull('id')
            ->limit((int) $limit)
            ->get()
            ->toArray();

        return $balanceHistory;
    }

}
