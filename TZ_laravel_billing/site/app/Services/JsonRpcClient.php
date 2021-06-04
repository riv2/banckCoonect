<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

// Взято с https://habr.com/ru/post/499626/
class JsonRpcClient
{
    const JSON_RPC_VERSION = '2.0';

    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'headers' => ['Content-Type' => 'application/json'],
            'base_uri' => config('app.rpc_base_url')
        ]);
    }

    public function send(string $method, array $params, $id = null): array
    {
        if (is_null($id)) {
            $id = time();
        }
        $response = $this->client
            ->post('', [
                RequestOptions::JSON => [
                    'jsonrpc' => self::JSON_RPC_VERSION,
                    'id' => $id,
                    'method' => $method,
                    'params' => $params
                ]
            ])->getBody()->getContents();
        return json_decode($response, true);
    }
}
