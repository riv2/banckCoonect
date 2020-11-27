<?php

namespace frontend\services;

use common\models\forms\LoginForm;
use frontend\services\traits\FormTrait;
use yii\helpers\ArrayHelper;
use yii\httpclient\Client;
use yii\web\Cookie;

/**
 * Сервис авторизации пользователя.
 */
class AuthService
{
    use FormTrait;

    /**
     * @var Client HTTP клиент.
     */
    protected $client;

    /**
     * Конструктор.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Авторизовать пользователя на основании заполненной формы.
     * @param LoginForm $loginForm
     * @return bool
     */
    public function login(LoginForm $loginForm): bool
    {
        $sendData = $loginForm->getAttributes();
        $response = $this->client->createRequest()
            ->setFormat(Client::FORMAT_JSON)
            ->setMethod('POST')
            ->setUrl(ArrayHelper::getValue(\Yii::$app->params, 'apiUrl') . '/login')
            ->setData($sendData)
            ->send();
        if ($response->getIsOk()) {
            $this->setCookies($response->data);
            return true;
        }
        $this->populateFormErrors($loginForm, $response->getData());

        return false;
    }

    /**
     * Logout пользователя.
     */
    public function logout(): void
    {
        $this->client->createRequest()
            ->setFormat(Client::FORMAT_JSON)
            ->setHeaders([
                'Authorization' => 'Bearer ' . \Yii::$app->request->getCookies()->get('access-token'),
            ])
            ->setMethod('POST')
            ->setUrl(ArrayHelper::getValue(\Yii::$app->params, 'apiUrl') . '/logout')
            ->send();
        $this->removeCookies();
    }

    /**
     * Установить куки авторизованного пользователя.
     * @param array $data
     */
    private function setCookies(array $data): void
    {
        \Yii::$app->response->cookies->add(new Cookie([
            'name' => 'access-token',
            'value' => ArrayHelper::getValue($data, 'accessToken'),
        ]));
        \Yii::$app->response->cookies->add(new Cookie([
            'name' => 'email',
            'value' => ArrayHelper::getValue($data, 'email'),
        ]));
    }

    /**
     * Удалить куки авторизованного пользователя.
     */
    private function removeCookies(): void
    {
        \Yii::$app->response->cookies->remove('access-token');
        \Yii::$app->response->cookies->remove('email');
    }
}
