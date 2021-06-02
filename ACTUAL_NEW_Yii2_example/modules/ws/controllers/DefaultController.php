<?php

namespace app\modules\ws\controllers;

use app\modules\ws\components\Manager;
use yii;
use yii\console\Controller;

/**
 * Class DefaultController
 *
 * @package app\modules\ws\controllers
 */
class DefaultController extends Controller
{
    /**
     * @var Manager
     */
    protected $manager;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->manager = Yii::createObject(Manager::className());
    }

    /**
     * Публикация сообщения в канал
     * @param string $channel
     * @param string $message
     * @return array|false
     */
    public function actionPublish($channel, $message)
    {
        try {
            $result = $this->manager->publish($channel, $message);
            $this->stdout($result);
            return 0;
        } catch (\Exception $ex) {
            $this->stderr($ex->getMessage());
            return 1;
        }
    }
}
