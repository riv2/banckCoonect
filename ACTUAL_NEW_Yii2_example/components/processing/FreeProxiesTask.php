<?php

namespace app\components\processing;
use app\components\base\ConsoleTaskInterface;
use app\models\enum\ParsingStatus;
use app\models\enum\Status;
use app\models\pool\ProxyParsingProject;
use app\models\reference\ConsoleTask;
use app\models\register\Parsing;
use yii\base\BaseObject;

class FreeProxiesTask extends BaseObject implements ConsoleTaskInterface
{
    public static function processTask(ConsoleTask $consoleTask)
    {
        $idsToDelete = ProxyParsingProject::find()
            ->alias('ppp')
            ->select('ppp.id')
            ->innerJoin(
                ['p' => Parsing::tableName()],
                'p.id = ppp.parsing_id'
            )
            ->andWhere(['NOT IN', 'p.parsing_status_id', [ParsingStatus::STATUS_PROCESSING, ParsingStatus::STATUS_QUEUED]])
            ->orWhere(['!=', 'p.status_id', Status::STATUS_ACTIVE])
            ->column();
        ProxyParsingProject::deleteAll(['id' => $idsToDelete]);
    }
}