<?php

namespace app\components\processing;
use app\components\base\ConsoleTaskInterface;
use app\components\DateTime;
use app\models\enum\ParsingStatus;
use app\models\pool\ReportMatching;
use app\models\reference\ConsoleTask;
use app\models\reference\ParsingProject;
use app\models\register\Parsing;
use yii\base\BaseObject;

class ReportMatchingKpiTask extends BaseObject implements ConsoleTaskInterface
{
    public static function processTask(ConsoleTask $consoleTask)
    {
        /** @var Parsing[] $parsings */
        $parsingProjects = ParsingProject::find()
            ->andWhere([
                'id' => Parsing::find()
                    ->select([
                        'parsing_project_id'
                    ])
                    ->andWhere(['parsing_type' => 'collecting'])
                    ->groupBy('parsing_project_id')
                    ->asArray()
                    ->column()
            ]);
        ;
        foreach ($parsingProjects->each() as $parsingProject) {
            ReportMatching::createFromProject($parsingProject);
        }
    }
}