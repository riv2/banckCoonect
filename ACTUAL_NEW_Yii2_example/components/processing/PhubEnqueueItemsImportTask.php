<?php

namespace app\components\processing;
use app\components\base\ConsoleTaskInterface;
use app\components\exchange\Exchange;
use app\models\reference\ConsoleTask;
use yii\base\BaseObject;

class PhubEnqueueItemsImportTask extends BaseObject implements ConsoleTaskInterface
{
    public static function processTask(ConsoleTask $consoleTask)
    {
        Exchange::runImport([
            'Items'        => []
        ]);
        return true;
    }
}