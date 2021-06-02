<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Http;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        if (app()->bound('sentry') && $this->shouldReport($exception)) {
            $eventId = app('sentry')->captureException($exception);
            $text = [];
            $text[] = '🚨 Ошибка в ' . config('app.env') . '!';
            if (app()->runningInConsole()) {
                $text[] = 'CLI: <code>' . join(' ', request()->server('argv', [])) . '</code>';
            } else {
                $text[] = auth()->check() && auth()->user()->pp ? 'Партнерская программа #' . auth()->user()->pp_id : 'Облако';
                $text[] = auth()->check() ? ', авторизован как <code>#' . auth()->user()->id . ' ' . auth()->user()->email . '</code>, роль: ' . auth()->user()->role : 'Не авторизован';
                $text[] = 'URL: <code>' . request()->method() . ' ' . request()->fullUrl() . '</code>';
            }
            $text[] = 'Ошибка: <code>' . $exception->getMessage() . '</code>';
            $text[] = 'Файл: <code>' . $exception->getFile() . ':' . $exception->getLine() . '</code>';

            $sentry_url = 'https://sentry.alfa-partners.ru/organizations/gocpa/issues/?query=' . $eventId;
            $text[] = '<a href="' . $sentry_url . '">Просмотреть в Sentry</a>';

            $text = join(PHP_EOL, $text);

            $token = config('telegram-logger.token');
            if (!app()->runningUnitTests() && $token) {
                $url = 'https://api.telegram.org/bot' . $token . '/sendMessage';
                Http::post($url, [
                    'text' => $text,
                    'chat_id' => config('telegram-logger.chat_id'),
                    'parse_mode' => 'html'
                ]);
            }
        }

        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        return parent::render($request, $exception);
    }
}
