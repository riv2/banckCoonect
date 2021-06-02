<?php

namespace app\components\processing;
use app\components\base\ConsoleTaskInterface;
use app\components\exchange\ProductHub;
use app\models\enum\Status;
use app\models\reference\CompetitorItem;
use app\models\reference\ConsoleTask;
use app\models\reference\Item;
use yii\base\BaseObject;

class ItemDeduplicateTask extends BaseObject implements ConsoleTaskInterface
{
    public static function processTask(ConsoleTask $consoleTask)
    {
        $phub = new ProductHub;

        foreach (Item::find()
                     ->andWhere([
                         'is_duplicate' => true
                     ])
                     ->batch(5000) as $items) {

            /** @var Item[] $items */
            foreach ($items as $item) {
                echo ".";
                if (!$item->main_id) {
                    $mainId = $phub->deduplicate($item->id);
                    $item->main_id = $mainId;
                    $item->save();
                    echo "[Phub request]";
                }

                if ($item->main_id) {
                    /** @var string[][] $competitorItems */
                    $competitorItems = CompetitorItem::find()
                        ->andWhere([
                            'item_id' => $item->id,
                            'status_id' => Status::STATUS_ACTIVE,
                        ])
                        ->asArray()
                        ->all();

                    if ($competitorItems && count($competitorItems) > 0) {
                        foreach ($competitorItems as $i => $competitorItem) {
                            unset($competitorItem['id']);
                            unset($competitorItem['index']);

                            $competitorItem['item_id'] = $item->main_id;
                            if (!CompetitorItem::find()
                                ->andWhere([
                                    'competitor_id' => $competitorItem['competitor_id'],
                                    'item_id'       => $competitorItem['item_id'],
                                    'status_id' => Status::STATUS_ACTIVE,
                                ])
                                ->exists()) {

                                try {
                                    CompetitorItem::getDb()
                                        ->createCommand()
                                        ->insert(CompetitorItem::tableName(), $competitorItem)
                                        ->execute();
                                    echo "[Urls clone ". count($competitorItems)."]";
                                } catch (yii\db\Exception $e) {
                                    echo "[Error insert]";
                                }

                            }
                        }
                    }
                }

            }
        }

    }
}