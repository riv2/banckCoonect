<?php

namespace app\components\processing;
use app\components\base\ConsoleTaskInterface;
use app\models\reference\ConsoleTask;
use GuzzleHttp\Client;
use yii\base\BaseObject;
use yii\helpers\Json;
use Yii;

class ClearRabbitTask extends BaseObject implements ConsoleTaskInterface
{
    public static function processTask(ConsoleTask $consoleTask)
    {
        $client = new Client;

        $amqp = \Yii::$app->params['amqp'];

        $response = $client->get("http://{$amqp['host']}:15672/api/queues?columns=name,consumers,messages,idle_since", [
            'auth' => [$amqp['login'],$amqp['password']]
        ]);

        $body = $response->getBody();
        $queues = Json::decode($body, true);

        if (isset($queues['error'])) {
            throw new \Exception($body);
        }

        $connection = new \AMQPConnection(Yii::$app->params['amqp']);
        $connection->connect();
        $channel = new \AMQPChannel($connection);
        $sutki = 3600 * 24;
        $now = strtotime("now");
        foreach ($queues as $queueInfo) {
            if (isset($queueInfo['idle_since'])) {
                if (intval($queueInfo['consumers']) === 0 && $now - strtotime($queueInfo['idle_since']) > $sutki) {
                    print_r($queueInfo);
                    $queue = new \AMQPQueue($channel);
                    $queue->setName($queueInfo['name']);
                    $queue->delete();
                }
            }
        }
        $connection->disconnect();
    }
}