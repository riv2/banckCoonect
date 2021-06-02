<?php

namespace app\components\processing;
use app\components\base\ConsoleTaskInterface;
use app\models\enum\ParsingStatus;
use app\models\enum\Status;
use app\models\reference\ConsoleTask;
use app\models\reference\Robot;
use app\models\register\Parsing;
use GuzzleHttp\Client;
use yii\base\BaseObject;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

class FreeRegionsTask extends BaseObject implements ConsoleTaskInterface
{
    public static function processTask(ConsoleTask $consoleTask)
    {
        $robotsIds = Robot::find()
            ->select('id')
            ->andWhere([
                'status_id' => Status::STATUS_ACTIVE,
            ])
            ->column();

        $parsingsInProcess = [];
        $client = new Client([
            'timeout'   => 60.0,
            'headers' => [
                'Accept'     => 'application/json',
            ]
        ]);
        foreach ($robotsIds as $id) {
                $response = $client->request('GET','http://' . $id . ':4000/');

                $json = Json::decode($response->getBody()->getContents());
                if ($json && is_array($json)) {
                    foreach ($json as $droidData) {
                        $parsingsInProcess[] = $droidData['parsingId'];
                    }
                }
        }
        $parsingsInProcess = array_filter($parsingsInProcess, function($id) {
            return strpos($id, 'test-') === FALSE;
        });
        Parsing::updateAll([
            'parsing_status_id' => ParsingStatus::STATUS_PROCESSING,
        ], [
            'id' => $parsingsInProcess,
        ]);
        Parsing::updateAll([
            'parsing_status_id' => ParsingStatus::STATUS_QUEUED,
        ], [
            'AND',
            ['parsing_status_id' => ParsingStatus::STATUS_PROCESSING],
            ['NOT IN', 'id', $parsingsInProcess],
        ]);
        return $parsingsInProcess;
        $parsingsQuery = Parsing::find()
            ->select([
                'ids' => 'json_agg(json_build_object(\'robot_id\', robot_id, \'id\', id))',
                'parsing_project_id'
            ])
            ->andWhere('region_id IS NOT NULL')
            ->andWhere([
                'parsing_status_id' => ParsingStatus::STATUS_PROCESSING,
                'status_id' => Status::STATUS_ACTIVE,
                'priority' => 0,
            ])
            ->groupBy(['region_id', 'parsing_project_id'])
            ->asArray();
        $projectsInProcess = [];
        $client = new Client([
            'timeout'   => 60.0,
            'headers' => [
                'Accept'     => 'application/json',
            ]
        ]);
        foreach ($parsingsQuery->each() as $parsingsData) {
            if (!in_array($parsingsData['parsing_project_id'], $projectsInProcess)) {
                $projectsInProcess[] = $parsingsData['parsing_project_id'];
                continue;
            }
            $dataArr = json_decode($parsingsData['ids'], true);
            Parsing::updateAll([
                'parsing_status_id' => ParsingStatus::STATUS_QUEUED
            ], [
                'id' => ArrayHelper::getColumn($dataArr, 'id')
            ]);
            foreach ($dataArr as $data) {
                $response = $client->request('GET','http://' . $data['robot_id'] . ':4000/kill-parsing/' . $data['id']);

                print_r($response->getBody()->getContents() . "\n");
            }
        }
    }
}