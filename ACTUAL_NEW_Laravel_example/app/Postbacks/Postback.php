<?php

namespace App\Postbacks;

use App\Interfaces\PostbackInterface;
use App\Models\Notify;
use App\Postbacks\Request\BaseRequest;
use App\Postbacks\Sender\BaseSender;
use App\Postbacks\Validator\BaseValidator;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Log;

class Postback implements PostbackInterface
{
    protected Notify $notify;
    public array $request;

    /**
     * @param Notify $notify
     * @return void
     */
    public function __construct(Notify $notify)
    {
        $this->notify = $notify;
    }

    /**
     *
     * @return boolean
     */
    public function handle(): bool
    {
        // тут чекнуть, что релейшны загружены
        // сформировать запрос

        if (env('APP_ENV') !== 'production') {
            $this->notify->notify_param->postback_url = 'https://gocpa.requestcatcher.com/test';
        }

        $request = $this->createRequest();
        if (empty($request)) {
            return false;
        }
        $this->request = $request->request();

        // отправить
        $sender = $this->createSender($request);
        $response = $sender->send();

        // сохранить
        $this->notify->responce_httpcode = $response->getStatusCode();
        $this->notify->sent_url = $request->request()['url'];
        $this->notify->sent_method = $this->notify->notify_param->method;
        $this->notify->sent_request = json_encode($request->request()['data']);
        $this->notify->sent_datetime = now();
        $this->notify->responce_body = mb_substr($response->getBody()->getContents(), 0, 30000);
        $this->notify->save();

        if ($response->getStatusCode() != 200 && $this->notify->sent_cnt < 10) {
            //Создаём новый постбэк, который отправится через 10 минут.
            $newNotify = $this->notify->replicate();
            $newNotify->datetime = now()->addMinutes(10);
            $newNotify->sent_cnt++;
            $newNotify->sent_url = null;
            $newNotify->sent_method = null;
            $newNotify->sent_request = null;
            $newNotify->sent_datetime = null;
            $newNotify->responce_httpcode = null;
            $newNotify->responce_body = null;
            $newNotify->save();
        }

        // проверить и вернуть успешность
        $validator = $this->createValidator($response);

        return $validator->validate();
    }

    protected function createRequest()
    {
        $className = 'App\Postbacks\Request\Request' . $this->notify->partner_id;
        if (class_exists($className)) {
            return new $className($this->notify);
        } else {
            return new BaseRequest($this->notify);
        }
    }

    protected function createSender(BaseRequest $request)
    {
        $className = 'App\Postbacks\Sender\Sender' . $this->notify->partner_id;
        if (class_exists($className)) {
            return new $className($request);
        } else {
            return new BaseSender($request);
        }
    }

    protected function createValidator(Response $response)
    {
        $className = 'App\Postbacks\Validator\Validator' . $this->notify->partner_id;
        if (class_exists($className)) {
            return new $className($response);
        } else {
            return new BaseValidator($response);
        }
    }
}
