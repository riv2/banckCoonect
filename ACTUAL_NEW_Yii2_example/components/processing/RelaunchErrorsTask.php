<?php

namespace app\components\processing;
use app\components\base\ConsoleTaskInterface;
use app\models\enum\ParsingStatus;
use app\models\enum\Status;
use app\models\pool\ParsingError;
use app\models\reference\ConsoleTask;
use app\models\register\Parsing;
use yii\base\BaseObject;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;

class RelaunchErrorsTask extends BaseObject implements ConsoleTaskInterface
{
    public static function processTask(ConsoleTask $consoleTask)
    {
        /** @var Parsing[] $parsings */
        $parsings = Parsing::find()
            ->andWhere([
                'parsing_status_id' => ParsingStatus::STATUS_DONE,
                'status_id'         => Status::STATUS_ACTIVE,
                'is_test'           => false,
                'next_attempt_id'   => null,
                'parallel_is_main'  => true,
            ])
            ->andWhere([
                '>', 'errors_count', 0
            ])
            ->andWhere([
                '<', 'attempt',     2
            ])
            ->all();


        foreach ($parsings as $parsing) {
            $childParsings = Parsing::find()
                ->andWhere([
                    'parallel_main_id' => $parsing->id
                ])
                ->all();

            $allDone = true;

            foreach ($childParsings as $childParsing) {
                $allDone = $allDone && ($childParsing->parsing_status_id === ParsingStatus::STATUS_DONE);
            }

            if (!$allDone) {
                continue;
            }

            $ids = ArrayHelper::getColumn($childParsings,'id');

            if (ParsingError::find()
                    ->andWhere([
                        'parsing_id' => $ids
                    ])
                    ->andWhere(['is not', 'url', null])
                    ->count() > 0) {

                $scope = Json::decode($parsing->scope_info);
                $scope['from_errors'] = $ids;
                $scope['attempt'] = $parsing->attempt + 1;
                $scope['name'] = 'Перепарсинг '.$parsing->name;
                $scope['regions'] = $parsing->region_id;

                $id = $parsing->parsingProject->execute($scope);

                if ($id) {
                    $parsing->next_attempt_id = $id;
                }
                $parsing->save();
            }
        }
    }
}