<?php

namespace App\Webhooks;

use App\Interfaces\WebhookInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

abstract class BaseWebhook implements WebhookInterface
{
    public Request $request;

    public array $validation_rules = [];
    public array $validation_messages = [];
    public array $validation_customAttributes = [];

    /**
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return void|\Illuminate\Support\MessageBag
     */
    public function validate()
    {
        $validator = Validator::make($this->request->all(), $this->validation_rules, $this->validation_messages, $this->validation_customAttributes);
        if ($validator->fails()) {
            return $validator->errors();
        }
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function handle()
    {
        Log::channel('telegram')->debug('Получен новый webhook ', $this->request->all());

        return response()->noContent(200);
    }
}
