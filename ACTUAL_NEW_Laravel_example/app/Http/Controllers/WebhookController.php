<?php

namespace App\Http\Controllers;

use App\Interfaces\WebhookInterface;
use Barryvdh\Debugbar\Facade as Debugbar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WebhookController extends Controller
{
    /**
     * Запускает класс обработки вебхука
     *
     * @param Request $request
     * @param string $webhookmethod
     * @return void|\Illuminate\Contracts\Support\Responsable
     */
    public function __invoke(Request $request, $webhookmethod = 'default')
    {
        Debugbar::disable();
        $webhookmethod = Str::ucfirst(Str::studly($webhookmethod));
        $class = 'App\\Webhooks\\' . $webhookmethod . 'Webhook';
        if (!class_exists($class) || !in_array(WebhookInterface::class, class_implements($class))) {
            Log::channel('telegram')->warning('Ошибка в вебхуке, метод ' . $webhookmethod . ' не найден!');
            abort(404, 'Webhook not found');
        }

        $rc = new $class($request);
        if ($response = $rc->validate()) {
            return $response;
        }
        return $rc->handle();
    }
}
