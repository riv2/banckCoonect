<?php

namespace app\components\processing;
use app\components\base\ConsoleTaskInterface;
use app\models\enum\HoradricCubeStatus;
use app\models\enum\PriceParsedStatus;
use app\models\enum\Status;
use app\models\pool\PriceParsed;
use app\models\reference\CompetitorItem;
use app\models\reference\ConsoleTask;
use app\models\register\Error;
use app\models\register\HoradricCube;
use yii\base\BaseObject;

class ProcessParsedPricesTask extends BaseObject implements ConsoleTaskInterface
{
    public static function processTask(ConsoleTask $consoleTask)
    {
        $thread = 0;
        $parsingProjects = [];

        PriceParsed::getDb()
            ->createCommand()
            ->update(PriceParsed::tableName(), [
                'price_parsed_status_id' => PriceParsedStatus::COLLECTING_FILTERED_OUT,
                'error_message' => 'Товар не в наличии',
            ], [
                'out_of_stock' => true,
                'price_parsed_status_id' => PriceParsedStatus::COLLECTING_NEW
            ])
            ->execute()
        ;

        $query = PriceParsed::find()
            ->select([
                'id',
                'competitor_id',
                'competitor_item_url',
            ])
            ->andWhere([
                'price_parsed_status_id' => PriceParsedStatus::COLLECTING_NEW,
                'out_of_stock' => false,
            ])
            ->orderBy(['extracted_at' => SORT_ASC])
            ->asArray()
        ;

        foreach ($query->batch(10000) as $parsedPrices) {
            echo count($parsedPrices) . "\n";

            $dataToUpdate = [
                'noUrl' => [],
                'urlAlreadyExists' => [],
                'urlInHoradricCube' => [],
                'urlInHoradricCubeMatched' => [],
                'urlInHoradricCubeWrong' => [],
                'toApi' => [],
            ];

            foreach ($parsedPrices as $parsedPrice) {

                if (!$parsedPrice['competitor_item_url']) {
                    $dataToUpdate['noUrl'][] = $parsedPrice['id'];
                    continue;
                } else {
                    $url = strtolower($parsedPrice['competitor_item_url']);
                    $url = trim(preg_replace('/https?:\/\/(www.)?/', '', $url));
                    $existsQuery = CompetitorItem::find()
                        ->andWhere([
                            'AND',
                            [
                                'competitor_id' => $parsedPrice['competitor_id'],
                                'status_id' => Status::STATUS_ACTIVE,
                            ],
                            ['ilike', 'url' , explode('?', $url)[0]],
                        ]);
//                    if ($parsedPrice['competitor_id'] === '3de0488a-d7ca-4a8d-aa1f-fe5036be33ce') {
//                        Error::logError($existsQuery->createCommand()->getRawSql());
//                    }

                    if ($existsQuery->exists()) {
                        $dataToUpdate['urlAlreadyExists'][] = $parsedPrice['id'];
                        continue;
                    }
//                    /** @var HoradricCube $horadricCube */
//                    $horadricCube = HoradricCube::find()
//                        ->andWhere(['ilike', 'competitor_item_url', $url])
//                        ->andWhere(['competitor_id' => $parsedPrice['competitor_id']])
//                        ->one();
//                    if ($horadricCube) {
//                        switch($horadricCube->horadric_cube_status_id) {
//                            case HoradricCubeStatus::STATUS_NEW:
//                                $dataToUpdate['urlInHoradricCube'][] = $parsedPrice['id'];
//                            break;
//                            case HoradricCubeStatus::STATUS_FILTERED_OUT:
//                            case HoradricCubeStatus::STATUS_MATCHED:
//                                $dataToUpdate['urlInHoradricCubeMatched'][] = $parsedPrice['id'];
//                            break;
//                            case HoradricCubeStatus::STATUS_WRONG:
//                                $dataToUpdate['urlInHoradricCubeWrong'][] = $parsedPrice['id'];
//                            break;
//                        }
//                        continue;
//                    }
                }

                $dataToUpdate['toApi'][] = $parsedPrice['id'];
            }

            foreach ($dataToUpdate as $key => $ids) {
                if (count($ids) > 0) {
                    $columns = [
                        'noUrl' => [
                            'error_message' => 'У товара не указан урл (competitor_item_url)',
                            'price_parsed_status_id' => PriceParsedStatus::COLLECTING_FILTERED_OUT,
                        ],
                        'urlAlreadyExists' => [
                            'error_message' => 'Товар есть в товарах конкурента',
                            'price_parsed_status_id' => PriceParsedStatus::COLLECTING_FILTERED_OUT,
                        ],
                        'urlInHoradricCube' => [
                            'error_message' => 'Товар уже на разборе',
                            'price_parsed_status_id' => PriceParsedStatus::COLLECTING_FILTERED_OUT,
                        ],
                        'urlInHoradricCubeMatched' => [
                            'error_message' => 'Товар уже сопоставлен',
                            'price_parsed_status_id' => PriceParsedStatus::COLLECTING_FILTERED_OUT,
                        ],
                        'urlInHoradricCubeWrong' => [
                            'error_message' => 'Товар уже в журнале несоответсвия',
                            'price_parsed_status_id' => PriceParsedStatus::COLLECTING_FILTERED_OUT,
                        ],
                        'toApi' => [
                            'price_parsed_status_id' => PriceParsedStatus::COLLECTING_API,
                        ],
                    ][$key];
                    PriceParsed::getDb()
                        ->createCommand()
                        ->update(
                            PriceParsed::tableName(),
                            $columns,
                            ['id' => $ids]
                        )
                        ->execute()
                    ;
                }
            }
        }
    }
}