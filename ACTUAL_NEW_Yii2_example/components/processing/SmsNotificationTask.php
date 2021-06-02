<?php

namespace app\components\processing;
use app\components\base\ConsoleTaskInterface;
use app\components\DateTime;
use app\models\enum\ParsingStatus;
use app\models\enum\Status;
use app\models\pool\ParsingError;
use app\models\pool\PriceParsed;
use app\models\reference\ConsoleTask;
use app\models\reference\ParsingProject;
use app\models\register\Parsing;
use GuzzleHttp\Client;
use yii\base\BaseObject;
use yii\base\UserException;
use yii\helpers\Json;

class SmsNotificationTask extends BaseObject implements ConsoleTaskInterface
{
    public static function processTask(ConsoleTask $consoleTask)
    {
        $currentHour = (int)date('H');
        if (8 > $currentHour || $currentHour > 23) {
            echo 'Задача работает с 08:00 до 23:00';
            return true;
        }
        $params = Json::decode($consoleTask->params);
        $login = isset($params['login']) ? $params['login'] : '';
        $password = isset($params['password']) ? $params['password'] : '';
        $phones = isset($params['phones']) ? $params['phones'] : '';

        if (!$phones || !count($phones)) {
            throw new UserException('Некому отправлять смс, укажите параметр \'phones\' с номерами телефона');
        }

        $endTimes = \Yii::$app->cache->get('active_parsings_data_times');
        $lateTime = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d 23:59:00'))->getTimestamp();

        $projectErrorsNames = [];
        $activeParsingsIds = Parsing::find()
            ->select('id')
            ->andWhere([
                'status_id' => Status::STATUS_ACTIVE,
                'parsing_status_id' => [ParsingStatus::STATUS_PROCESSING],
            ])
            ->column();

        $query = Parsing::find()
            ->alias('p')
            ->select([
                'p.id',
                'project_name'  => 'pp.name',
                'errors_count'  => 'pe.errors_count',
                'errors_limit'  => 'pp.errors_per_hour_available',
                'in_stocks'     => 'ppp.in_stock_count',
                'out_of_stocks' => 'ppp.out_of_stock_count',
            ])
            ->from([
                'p' => Parsing::find()
                    ->select([
                        'id',
                        'parsing_project_id',
                        'name',
                        'created_at'
                    ])
                    ->andWhere(['id' => $activeParsingsIds])
            ])
            ->leftJoin([
                'pe' => ParsingError::find()
                    ->select([
                        'parsing_id',
                        'COUNT(*) as errors_count',
                    ])
                    ->andWhere(['parsing_id' => $activeParsingsIds])
                    ->andWhere(['>', 'created_at', date('Y-m-d H:i:s', strtotime('1 hour ago'))])
                    ->groupBy('parsing_id')
            ], 'pe.parsing_id = p.id')
            ->leftJoin([
                'ppp' => PriceParsed::find()
                    ->select([
                        'parsing_id',
                        'COUNT(id) filter (where out_of_stock = false) as in_stock_count',
                        'COUNT(id) filter (where out_of_stock = true) as out_of_stock_count',
                    ])
                    ->andWhere([
                        'parsing_id' => $activeParsingsIds
                    ])
                    ->andWhere(['>', 'created_at', date('Y-m-d H:i:s', strtotime('1 hour ago'))])
                    ->groupBy('parsing_id')
            ], 'ppp.parsing_id = p.id')
            ->leftJoin([
                'pp' => ParsingProject::tableName(),
            ], 'pp.id = p.parsing_project_id')
            ->andWhere(['pp.signals_enabled' => true])
            ->orderBy('p.created_at, p.name')
            ->asArray();

        foreach ($query->each() as $parsingData) {
            if (isset($projectErrorsNames[$parsingData['project_name']])) {
                continue;
            }
            if ($parsingData['errors_count'] > $parsingData['errors_limit']) {
                $projectErrorsNames[$parsingData['project_name']] = '(ош)';
            } else if ($parsingData['out_of_stocks'] > 0 && ($parsingData['in_stocks']/($parsingData['out_of_stocks'] + $parsingData['in_stocks'])) * 100 <= 10) {
                $projectErrorsNames[$parsingData['project_name']] = '(нал)';
            } else if (!isset($endTimes[$parsingData['id']]) || $endTimes[$parsingData['id']] > $lateTime) {
                $projectErrorsNames[$parsingData['project_name']] = '';
            }
        }

        $text = '(' . count($projectErrorsNames) . ')';
        foreach ($projectErrorsNames as $projectName => $reason) {
            $text .= substr(explode(
                '_',
                str_replace([' ', ',', '-'], '_', $projectName)
            )[0], 0, 5) . $reason . ', ';
        }
        echo $text . '<br>';

        if (count($projectErrorsNames) === 0) {
            return false;
        }

        $client = new Client;

        $json = [];
        foreach ($phones as $phone) {
            $json[] = [
                'to' => $phone,
                'text' => $text,
            ];
        }

        $response = $client->post('http://sms.vseinstrumenti.ru/api/v1/sms/send-to', [
            'auth' => [$login, $password],
            'json' => $json,
        ]);

        $body = $response->getBody()->getContents();
        print_r($body);
        return true;
    }
}