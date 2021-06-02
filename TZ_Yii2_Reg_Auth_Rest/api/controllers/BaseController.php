<?php

namespace api\controllers;

use api\filters\HttpBearerAuth;
use yii\filters\Cors;
use yii\helpers\ArrayHelper;
use yii\rest\Controller;

/**
 * REST контроллер с базовой конфигурацией.
 */
abstract class BaseController extends Controller
{
    /**
     * @var array|null Действия, для который требуется авторизация.
     */
    protected const AUTH_ONLY = null;

    /**
     * @var array|null Действия, исключаемые из тех, для которых требуется авторизация.
     */
    protected const AUTH_EXCEPT = null;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'corsFilter' => [
                'class' => Cors::class,
                'cors' => [
                    'Origin' => [ArrayHelper::getValue(\Yii::$app->params, 'frontendUrl')],
                    'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                    'Access-Control-Request-Headers' => ['*'],
                    'Access-Control-Allow-Credentials' => null,
                    'Access-Control-Max-Age' => 86400,
                    'Access-Control-Expose-Headers' => [],
                ],
            ],
            'authenticator' => [
                'class' => HttpBearerAuth::class,
                'only' => static::AUTH_ONLY,
                'except' => static::AUTH_EXCEPT,
                'exceptMethods' => ['OPTIONS'],
            ],
        ]);
    }
}
