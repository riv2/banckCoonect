<?php

namespace app\components\processing;
use app\components\base\ConsoleTaskInterface;
use app\models\pool\LogKpi;
use app\models\pool\LogPriceCalculation;
use app\models\reference\ConsoleTask;
use app\models\register\Error;
use yii\base\BaseObject;
use yii\helpers\Json;
use Yii;

class CalcLogTask extends BaseObject implements ConsoleTaskInterface
{
    public static function processTask(ConsoleTask $consoleTask)
    {
        $connection = new \AMQPConnection(Yii::$app->params['amqp']);
        $connection->connect();

        $channel = new \AMQPChannel($connection);

        $queue = new \AMQPQueue($channel);
        $queue->setName(getenv('RABBIT_CALC_LOG_QUEUE'));
        $queue->setFlags(AMQP_DURABLE);

        for ($i=0 ; $i<20000; $i++) {
            $item  = 1;
            $items = [];
            while (count($items) < 1000 && $item) {
                $item = $queue->get(AMQP_AUTOACK);
                if ($item) {
                    $itemJson = Json::decode($item->getBody());
                    if (!isset($itemJson['regions'])) {
                        $itemJson['regions'] = '';
                    }
                    unset($itemJson[0],$itemJson['0']);
                    $items[] = $itemJson;

                    LogKpi::updateAll([
                        'is_used_in_calc' => true,
                        'url' => $itemJson['url'],
                        'price' => $itemJson['price_refined'],
                        'extracted_at' => $itemJson['extracted_at'],
                        'price_refined_id' => $itemJson['price_refined_id'],
                        'calculated_at' => $itemJson['created_at'],
                        'out_of_stock' => $itemJson['out_of_stock'],
                    ], [
                        'project_execution_id' => $itemJson['project_execution_id'],
                        'competitor_id' => $itemJson['competitor_id'],
                        'item_id' => $itemJson['item_id'],
                    ]);
                }
            }
            if (count($items) > 0) {
                foreach ($items as $item) {
                    LogPriceCalculation::getDb()
                        ->createCommand()
                        ->insert(LogPriceCalculation::tableName(), $item)
                        ->execute();
                }
            }
        }

        $connection->disconnect();
    }
}