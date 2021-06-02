<?php

namespace app\components;

use yii\helpers\Json;

/**
 * Component for storing sessions in Redis as JSON.
 */
class Session extends \yii\redis\Session
{
    /**
     * @inheritdoc
     */
    protected function calculateKey($id)
    {
        return $this->keyPrefix . $id;
    }

    /**
     * @inheritdoc
     */
    public function readSession($id)
    {
        $json = $this->redis->executeCommand('GET', [$this->calculateKey($id)]);
        $_SESSION = Json::decode($json, true);
        return isset($_SESSION) && !empty($_SESSION) ? session_encode() : '';
    }

    /**
     * @inheritdoc
     */
    public function writeSession($id, $data)
    {
        $json = Json::encode($_SESSION);
        return (bool) $this->redis->executeCommand('SET', [$this->calculateKey($id), $json, 'EX', $this->getTimeout()]);
    }

    /**
     * @inheritdoc
     * https://github.com/yiisoft/yii/issues/2376
     */
    public function regenerateID($deleteOldSession = false)
    {
        if (session_status() != PHP_SESSION_ACTIVE) {
            @session_regenerate_id($deleteOldSession);
        }
    }
}
