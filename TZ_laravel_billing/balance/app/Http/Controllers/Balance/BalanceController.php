<?php

namespace App\Http\Controllers\Balance;

use AvtoDev\JsonRpc\Requests\RequestInterface;
use App\Providers\BalanceServiceProvider;
use Illuminate\Foundation\Application;

class BalanceController
{
    private $balanceServiceProvider;

    public function __construct(Application $app)
    {
        $this->balanceServiceProvider = new BalanceServiceProvider($app);
    }

    public function userBalance(RequestInterface $request)
    {
        $user_id        = 0;
        $params         = $request->getParams();

        if (isset($params->user_id)) {
            $user_id = (int)$params->user_id;
        }

        return $this->balanceServiceProvider->userBalance($user_id);
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

        return $this->balanceServiceProvider->history($limit);
    }
}
