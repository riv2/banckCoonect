<?php

namespace app\components\processing;
use app\components\base\ConsoleTaskInterface;
use app\components\exchange\Exchange;
use app\models\reference\ConsoleTask;
use yii\base\BaseObject;

class PhubItemsImportTask extends BaseObject implements ConsoleTaskInterface
{
    public static function processTask(ConsoleTask $consoleTask)
    {
        for ($i = 0; $i < 5; $i ++) {
            Exchange::runImport([
                'Items' => [
                    'autoEnqueue' => false,
                    'importQueue' => 1000
                ]
            ]);
        }
        return true;
    }
}