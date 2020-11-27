<?php

namespace frontend\services;

use common\models\forms\SignupForm;
use frontend\services\traits\FormTrait;
use yii\helpers\ArrayHelper;
use yii\httpclient\Client;

/**
 * Сервис регистрации пользователя.
 */
class SignupService
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
     * Зарегистрировать пользвателя на основании заполненной формы.
     * @param SignupForm $signupForm
     * @return bool
     */
    public function signup(SignupForm $signupForm): bool
    {
        $sendData = $signupForm->getAttributes();
        $response = $this->client->createRequest()
            ->setFormat(Client::FORMAT_JSON)
            ->setMethod('POST')
            ->setUrl(ArrayHelper::getValue(\Yii::$app->params, 'apiUrl') . '/signup')
            ->setData($sendData)
            ->send();

        if ($response->getIsOk()) {
            return true;
        }
        $this->populateFormErrors($signupForm, $response->getData());

        return false;
    }
}
