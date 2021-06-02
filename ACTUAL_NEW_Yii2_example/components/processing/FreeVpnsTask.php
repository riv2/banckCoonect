<?php

namespace app\components\processing;
use app\components\base\ConsoleTaskInterface;
use app\models\enum\ParsingStatus;
use app\models\enum\Status;
use app\models\pool\VpnCompetitor;
use app\models\reference\ConsoleTask;
use app\models\register\Parsing;
use GuzzleHttp\Client;
use yii\base\BaseObject;

class FreeVpnsTask extends BaseObject implements ConsoleTaskInterface
{
    public static function processTask(ConsoleTask $consoleTask)
    {
        $idsToDelete = VpnCompetitor::find()
            ->alias('vc')
            ->select('vc.id')
            ->innerJoin(
                ['p' => Parsing::tableName()],
                'p.id = vc.parsing_id'
            )
            ->andWhere(['NOT IN', 'p.parsing_status_id', [ParsingStatus::STATUS_PROCESSING, ParsingStatus::STATUS_QUEUED]])
            ->orWhere(['!=', 'p.status_id', Status::STATUS_ACTIVE])
            ->column();
        VpnCompetitor::deleteAll(['id' => $idsToDelete]);
    }}