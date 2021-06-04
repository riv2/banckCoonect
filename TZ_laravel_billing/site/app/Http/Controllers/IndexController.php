<?php

namespace App\Http\Controllers;

use App\Services\JsonRpcClient;

class IndexController extends Controller
{
    protected $client;
    protected $userId = 1;
    protected $limit  = 50;
    protected $id     = 1;

    public function __construct(JsonRpcClient $client)
    {
        $this->client = $client;
    }

    public function show()
    {
        $result = [];

        $userBalance = $this->client->send('balance.userBalance', ['user_id' => $this->userId], $this->id);
        $history     = $this->client->send('balance.history', ['limit' => $this->limit]);

        $result['userBalance'] = $userBalance;
        $result['history']     = $history;

        dd($result);

        // Тут уже все по стандарту должно быть - вьющка - вывод и т д. В условие ТЗ не входило написание вьюшки - только вывод.

    }
}
