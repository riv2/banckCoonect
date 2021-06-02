<?php

namespace app\components\processing;
use app\components\base\ConsoleTaskInterface;
use app\models\pool\LogProjectExecution;
use app\models\reference\ConsoleTask;
use yii\base\BaseObject;
use yii\helpers\Json;
use Yii;

class CalcLogExecTask extends BaseObject implements ConsoleTaskInterface
{
    public static function processTask(ConsoleTask $consoleTask)
    {
        $connection = new \AMQPConnection(Yii::$app->params['amqp']);
        $connection->connect();

        $channel = new \AMQPChannel($connection);

        $queue = new \AMQPQueue($channel);
        $queue->setName(getenv('RABBIT_CALC_LOG_EXEC_QUEUE'));
        $queue->setFlags(AMQP_DURABLE);

        for ($i=0 ; $i<2000; $i++) {
            $item  = 1;
            $items = [];
            while (count($items) < 1000 && $item) {
                $item = $queue->get(AMQP_AUTOACK);
                if ($item) {
                    $itemJson = Json::decode($item->getBody());
                    if ($itemJson) {
                        unset($itemJson[0],$itemJson['0']);
                        if (isset($itemJson['created_at']) && is_array($itemJson['created_at'])) {
                            $itemJson['created_at'] = date('Y-m-d H:i:s');
                        }
                        $items[] = $itemJson;
                    }
                }
            }
            if (count($items) > 0) {
                LogProjectExecution::getDb()->createCommand()->batchInsert(LogProjectExecution::tableName(), array_keys($items[0]), $items)->execute();
            }
        }

        $connection->disconnect();
    }
}