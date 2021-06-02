<?php

namespace App\Postbacks\Sender;

use App\Postbacks\Request\BaseRequest;
use Exception;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class BaseSender
{
    protected BaseRequest $request;

    public function __construct(BaseRequest $request)
    {
        $this->request = $request;
    }

    /**
     * @return ResponseInterface
     * @throws Exception
     */
    public function send(): ResponseInterface
    {
        $sender = new Client([
            'verify' => false,
            'http_errors' => false,
        ]);

        $data = $this->request->request();

        if ($data['method'] == 'get') {
            $result = $sender->get($data['url']);
        } elseif ($data['method'] == 'post') {
            $result = $sender->post($data['url'], $data['data']);
        } else {
            throw new Exception('Unknown method "' . $data['method'] . '" in postback');
        }

        return $result;
    }
}
