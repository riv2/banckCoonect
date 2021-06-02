<?php
namespace app\commands;

use app\processing\ItemProcessing;
use yii\console\Controller;

class ItemController extends Controller
{


    /**
     * Обновить ранжирование товаров из Ириды
     */

    public function actionUpdateRanks() {
        if ($this->processIsRun('item/update-ranks') > 2) {
            return;
        }
        ItemProcessing::updateRanks();
    }

    /**
     * Проверяем, что процесс запущен
     * @param string $processName Имя процесса
     * @return bool
     * @return boolean
     */
    public function processIsRun($processName)
    {
        $result = [];
        exec("ps aux | grep -v grep | grep \"$processName\"", $result);
        $count = count($result);
        return ceil($count);
    }

}