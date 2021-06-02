<?php

namespace app\modules\ws\components;


use app\models\reference\User;
use yii;
use yii\web\View;
use yii\base\Component;
use yii\helpers\Json;

/**
 * Class Manager
 *
 * @package app\modules\ws\components
 */
class Manager extends Component
{
    /**
     * @var string
     */
    public $channelForMessages = 'ws:messages';

    /**
     * @var string
     */
    public $sessionKeyPrefix = 'prc:session:';

    /**
     * @var string
     */
    public $activeSessionsKey = 'prc:active_sessions';

    /**
     * Публикация сообщения в канал
     * @param string $channel
     * @param mixed $message
     * @return integer
     */
    public function publish($channel, $message)
    {
        $data = Json::encode(['channel' => $channel, 'message' => $message]);
        return Yii::$app->redis->executeCommand('PUBLISH', [$this->channelForMessages, $data]);
    }
    
    /**
     * Публикация события в канал пользователя
     * @param string|\app\components\User|\app\models\reference\User $user
     * @param string $event
     * @param mixed $data
     * @return integer
     */
    public function publishToUser($user, $event, $data)
    {
        $userId = $user;
        if ($user instanceof \app\components\User) {
            $userId = $user->identity->id;
        } else if ($user instanceof User) {
            $userId = $user->id;
        } 
        $channel = 'user#'.$userId;
        return $this->publishEvent($channel, $event, $data);
    }

    /**
     * Публикация события в канал
     * @param string $channel
     * @param string $event
     * @param mixed $data
     * @return integer
     */
    public function publishEvent($channel, $event, $data)
    {
        return $this->publish($channel,  array_merge(['event' => $event], $data));
    }


    public function registerAssets(View $view) {
        $view->registerJs("window.wsOptions = {'wsUserChannels':[]};", View::POS_HEAD);
        $wsChannels = Yii::$app->user->getWsChannels();
        if ($wsChannels) {
            $view->registerJs('window.wsOptions.wsUserChannels = ' . \yii\helpers\Json::encode($wsChannels) . ';', View::POS_HEAD);
        }
        \app\modules\ws\assets\Asset::register($view);
    }

    /**
     * Получение списка идентификаторов активных пользователей
     * @return array
     */
    public function getOnlineUsersIds()
    {
        $sessionsIds = Yii::$app->redis->executeCommand('SMEMBERS', [$this->activeSessionsKey]);
        if (!is_array($sessionsIds) || !$sessionsIds) {
            return [];
        }
        Yii::$app->redis->executeCommand('MULTI');
        foreach ($sessionsIds as $sessionId) {
            Yii::$app->redis->executeCommand('GET', [$this->sessionKeyPrefix . $sessionId]);
        }
        $sessions = Yii::$app->redis->executeCommand('EXEC');
        $result = [];
        foreach ($sessions as $session) {
            if ($session) {
                $sessionData = Json::decode($session);
                if (isset($sessionData['__id'])) {
                    $result[$sessionData['__id']] = $sessionData['__id'];
                }
            }
        }
        return $result;
    }
}
