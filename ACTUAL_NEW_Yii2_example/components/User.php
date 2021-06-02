<?php

namespace app\components;

use app\models\register\Task;
use yii;

/**
 * Компонент User
 *
 * @property \app\models\reference\User $identity Модель пользователя
 * @method \app\models\reference\User getIdentity() getIdentity() Возвращает модель пользователя
 * @package app\components
 */
class User extends \yii\web\User
{
    const WS_CHANNEL_COMMON = 'common';
    /**
     * @var string ключ сессии для хранения списка каналов, на которые подписан пользователь
     */
    public $sessionKeyForWsChannels = 'ws-channels';

    /**
     * Получение списка каналов, на которые подписан пользователь
     * @return array
     */
    public function getWsChannels()
    {
        return Yii::$app->session->get($this->sessionKeyForWsChannels, []);
    }

    /**
     * Установка списка каналов, на которые подписан пользователь
     * @param array $channels
     */
    public function setWsChannels($channels)
    {
        Yii::$app->session->set($this->sessionKeyForWsChannels, $channels);
    }

    /**
     * @inheritdoc
     * @param \app\models\reference\User $identity
     */
    protected function afterLogin($identity, $cookieBased, $duration)
    {
        parent::afterLogin($identity, $cookieBased, $duration);

        // Подписываем пользователя на каналы WS-сервера
        $this->setWsChannels([$identity->getPersonalWsChannelName(), self::WS_CHANNEL_COMMON]);
    }
}