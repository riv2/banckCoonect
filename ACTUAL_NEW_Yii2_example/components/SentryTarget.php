<?php

namespace app\components;

use mito\sentry\Target;
use Yii;
use yii\helpers\VarDumper;
use yii\log\Logger;
use yii\web\Request;

/**
 * Class SentryTarget
 */
class SentryTarget extends Target
{
    /**
     * @inheritDoc
     */
    public function export()
    {
        foreach ($this->messages as $message) {
            list($context, $level, $category, $timestamp, $traces) = $message;

            $data = [
                'level' => static::getLevelName($level),
                'timestamp' => $timestamp,
                'tags' => [
                    'category' => $category,
                ],
                'extra' => [
                    'cookie'    => !empty($_COOKIE) ? $_COOKIE : '',
                    'server'    => !empty($_SERVER) ? $_SERVER : '',
                    'session'   => !empty($_SESSION) ? $_SESSION : '',
                ],
                'user' => [
                    'id'    => Yii::$app->has('user') ? Yii::$app->user->id : 'non',
                    'name'  => Yii::$app->has('user') ? (string)\Yii::$app->user->getIdentity() : 'console',
                    'ip'    => Yii::$app->getRequest() instanceof Request ? Yii::$app->getRequest()->getUserIP() : '-',
                ],
            ];

            if ($context instanceof \Throwable || $context instanceof \Exception) {
                $this->sentry->captureException($context, $data);
                continue;
            } elseif (isset($context['msg'])) {
                $data['message'] = $context['msg'];
                $extra = $context;
                unset($extra['msg']);
                $data['extra'] = $extra;
            } else {
                $data['message'] = is_string($context) ? $context : VarDumper::export($context);
                if (is_array($context)) {
                    $data['extra'] = $context;
                }
            }

            $this->sentry->capture($data, $traces);
        }
    }

    /**
     * @inheritDoc
     * @param int $level
     * @return mixed|string
     */
    public static function getLevelName($level)
    {
        static $levels = [
            Logger::LEVEL_ERROR => 'error',
            Logger::LEVEL_WARNING => 'warning',
            Logger::LEVEL_INFO => 'info',
        ];

        return isset($levels[$level]) ? $levels[$level] : 'error';
    }
}